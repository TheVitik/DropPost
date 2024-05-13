<?php

namespace App\Http\Controllers\ChannelSettings;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActiveAIBotRequest;
use App\Http\Requests\ActiveCopyPostRequest;
use App\Http\Requests\SetAIBotRequest;
use App\Http\Requests\StoreAIBotRequest;
use App\Http\Requests\UpdateAIBotRequest;
use App\Http\Resources\AIBotResource;
use App\Models\AIBot;
use App\Models\Channel;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class AIBotController extends Controller
{
    /**
     * Activate or deactivate AI bot for the channel.
     */
    public function active(ActiveAIBotRequest $request, Project $project, Channel $channel): JsonResponse
    {
        $channel->update($request->validated());

        return response()->json(null, 204);
    }

    /**
     * Update AI bot for the channel.
     */
    public function set(SetAIBotRequest $request, Project $project, Channel $channel): JsonResponse
    {
        $channel->update($request->validated());

        return response()->json(null, 204);
    }
}
