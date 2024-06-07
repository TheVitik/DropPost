<?php

namespace App\Http\Controllers\ChannelSettings;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActiveAIBotRequest;
use App\Http\Requests\SetAIBotRequest;
use App\Jobs\AIBot\BotSendPost;
use App\Models\AIBot;
use App\Models\Channel;
use App\Models\Project;
use App\Services\BotPostPlanner;
use App\Services\ContentFormatter;
use App\Services\OpenAI\GPT3TurboBot;
use Illuminate\Http\JsonResponse;
use Telegram\Bot\Api;

class AIBotController extends Controller
{
    public function __construct(private BotPostPlanner $planner)
    {
    }

    /**
     * Activate AI bot for the channel.
     * @throws \Exception
     */
    public function activate(Project $project, Channel $channel): JsonResponse
    {
        $channel->update(['is_bot_active' => true]);

        $this->planner->plan($channel->aiBot, $channel);

        return response()->json(null, 204);
    }

    /**
     * Deactivate AI bot for the channel.
     */
    public function deactivate(Project $project, Channel $channel): JsonResponse
    {
        $channel->update(['is_bot_active' => false]);

        // TODO: remove planned jobs

        return response()->json(null, 204);
    }

    /**
     * Update AI bot for the channel.
     * @throws \Exception
     */
    public function set(SetAIBotRequest $request, Project $project, Channel $channel): JsonResponse
    {
        if ($request->ai_bot_id !== null) {
            $bot = AIBot::findOrFail($request->ai_bot_id);
            if ($channel->ai_bot_id === null && $request->ai_bot_id !== null) {
                $channel->update(['is_bot_active' => true]);
            }
            $channel->update(['ai_bot_id' => $request->ai_bot_id]);
            $this->planner->plan($bot, $channel);
        } else {
            $channel->update(['ai_bot_id' => null, 'is_bot_active' => false]);
        }

        return response()->json(null, 204);
    }
}
