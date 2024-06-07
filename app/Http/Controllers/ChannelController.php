<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChannelRequest;
use App\Http\Resources\ChannelListResource;
use App\Http\Resources\ChannelResource;
use App\Models\Channel;
use App\Models\Post;
use App\Models\Project;
use App\Services\Telegram\TelegramChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Facades\Telegram;

class ChannelController extends Controller
{
    public function __construct(private TelegramChatService $chatService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        return response()->json(ChannelResource::collection($project->channels));
    }

    public function listForFilter(Project $project): JsonResponse
    {
        $channels = $project->channels;
        return response()->json(ChannelListResource::collection($channels));
    }

    /**
     * Store a newly created resource in storage.
     * @throws TelegramSDKException
     */
    public function store(StoreChannelRequest $request, Project $project): JsonResponse
    {
        // Check is channel already exists
        $channelExists = $project->channels()->where(function ($query) use ($request) {
            $query->where('username', $request->username)
                ->orWhere('telegram_chat_id', $request->username);
        })->exists();

        if ($channelExists) {
            return response()->json(['message' => 'Канал вже існує'], 409);
        }

        try {
            $chat = $this->chatService->getChat($request->username, true);
        } catch (TelegramSDKException $e) {
            Log::channel('telegram')->error($e->getMessage());
            return response()->json(['message' => 'Канал з таким ID не знайдено'], 404);
        }
        try {
            $membersCount = $this->chatService->getMembersCount($request->username, true);
        } catch (TelegramSDKException $e) {
            Log::channel('telegram')->error($e->getMessage());
            return response()->json(['message' => 'Не вдалося отримати дані каналу'], 504);
        }

        try {
            $photo = $this->chatService->getPhoto($chat->photo->big_file_id);
        } catch (\Exception $e) {
            Log::channel('telegram')->error($e->getMessage());
            $photo = null;
        }
        $channel = $project->channels()->create([
            'telegram_chat_id' => $chat->id,
            'name' => $chat->title,
            'description' => $chat->description,
            'username' => $chat->username,
            'type' => $chat->type,
            'photo_path' => $photo,
            'invite_link' => $chat->invite_link,
            'members_count' => $membersCount,
        ]);

        return response()->json(new ChannelResource($channel), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Channel $channel): JsonResponse
    {
        return response()->json(new ChannelResource($channel));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Channel $channel): JsonResponse
    {
        $channel->delete();

        return response()->json(null, 204);
    }

    /**
     * Synchronize the channel with the Telegram API.
     * @throws TelegramSDKException
     */
    public function synchronize(Project $project, Channel $channel): JsonResponse
    {
        $chat = $this->chatService->getChat($channel->telegram_chat_id);
        $membersCount = $this->chatService->getMembersCount($channel->telegram_chat_id);

        $channel->update([
            'title' => $chat->title,
            'description' => $chat->description,
            'username' => $chat->username,
            'members_count' => $membersCount,
        ]);

        return response()->json(new ChannelResource($channel));
    }
}
