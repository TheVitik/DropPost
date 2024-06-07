<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Channel;
use App\Models\Post;
use App\Models\PostTemplate;
use App\Services\ContentFormatter;
use App\Services\FileProcessor;
use App\Services\Telegram\TelegramMessageService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function __construct(
        private ContentFormatter $formatter,
        private TelegramMessageService $service,
        private FileProcessor $processor
    ) {
    }

    public function index(Channel $channel)
    {
        $posts = $channel->posts()->whereNull('deleted_at')->latest()->get();
        return response()->json(PostResource::collection($posts));
    }

    public function store(StorePostRequest $request)
    {
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $uploadedFiles = $this->processor->uploadFiles($files);
            $request->merge(['media' => $uploadedFiles]);
        }

        $formattedContent = '';
        if ($request->content_html) {
            $formattedContent = $this->formatter->htmlToTelegram($request->content_html);
        }

        $channels = Channel::findOrFail($request->channels);

        $failedChannels = collect();

        $data = [
            'content_html' => $request->content_html,
            'content_json' => '[]', // No need to convert for now
            'is_advertisement' => $request->is_advertisement,
            'is_draft' => $request->is_draft,
            'media' => $request->media ?? [],
        ];


        if ($request->has('plan_publish_date')) {
            $data['publish_at'] = Carbon::parse($request->plan_publish_date);
        }

        if ($request->has('plan_delete_date')) {
            $data['delete_at'] = Carbon::parse($request->plan_delete_date);
        }

        foreach ($channels as $channel) {
            try {
                DB::beginTransaction();
                $post = $channel->posts()->create($data);
                if ($request->is_template) {
                    PostTemplate::create([
                        'name' => 'Збережений шаблон публікації',
                        'content_html' => $request->content_html,
                        'channel_id' => $channel->id
                    ]);
                }
                if (!$request->is_draft) {
                    if ($request->plan_publish_date) {
                        $this->service->sendPostLater(
                            $post,
                            $formattedContent,
                            $data['publish_at']
                        );
                    } else {
                        $this->service->sendPost($post, $formattedContent);
                    }

                    if ($request->plan_delete_date) {
                        $this->service->deletePostLater(
                            $post,
                            $data['delete_at']
                        );
                    }
                }


                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $failedChannels->push($channel);
                Log::channel('telegram')->error($e->getMessage());
            }
        }

        if ($failedChannels->isNotEmpty()) {
            $count = $failedChannels->count();
            if ($count === 1) {
                return response()->json([
                    'message' => 'Не вдалося надіслати публікацію до каналу "' . $failedChannels->first()->name . '"'
                ], 500);
            }
            $channels = $failedChannels->map(fn(Channel $channel) => $channel->name)->implode(', ');
            return response()->json([
                'message' => "Не вдалося надіслати публікації до $count каналів: \"$channels\""
            ], 500);
        }

        return response()->json([], 201);
    }

    public function show(Post $post)
    {
        return response()->json(new PostResource($post));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        if (!$post->telegram_message_id) {
            return response()->json([
                'message' => 'Публікація не була надіслана в Telegram'
            ], 400);
        }

        $formattedContent = '';
        if ($request->content_html) {
            $formattedContent = $this->formatter->htmlToTelegram($request->content_html);
        }

        try {
            DB::beginTransaction();
            $post->content_html = $request->content_html;
            if ($post->isDirty('content_html')) {
                $post->save();
                $this->service->updatePost($post, $formattedContent);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('telegram')->error($e->getMessage());
            return response()->json([
                'message' => 'Не вдалося оновити публікацію'
            ], 500);
        }

        return response()->json(new PostResource($post));
    }

    public function destroy(Post $post): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            if ($post->telegram_message_id) {
                $post->delete();
                $this->service->deletePost($post);
            } else {
                $post->forceDelete();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('telegram')->error($e->getMessage());
            return response()->json([
                'message' => 'Не вдалося видалити публікацію'
            ], 500);
        }

        return response()->json([], 204);
    }
}
