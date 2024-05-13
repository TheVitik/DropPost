<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChannelRequest;
use App\Http\Resources\ChannelResource;
use App\Models\Channel;
use App\Models\Project;
use App\Services\Telegram\TelegramChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    /**
     * Store a newly created resource in storage.
     * @throws TelegramSDKException
     */
    public function store(StoreChannelRequest $request, Project $project): JsonResponse
    {
        $chat = $this->chatService->getChat($request->chat_id);
        $membersCount = $this->chatService->getMembersCount($request->chat_id);

        return DB::transaction(function () use ($chat, $membersCount, $project) {
            $channel = $project->channels()->create([
                'telegram_chat_id' => $chat->id,
                'title' => $chat->title,
                'description' => $chat->description,
                'username' => $chat->username,
                'type' => $chat->type,
                'members_count' => $membersCount,
            ]);

            return response()->json(new ChannelResource($channel), 201);
        });
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
