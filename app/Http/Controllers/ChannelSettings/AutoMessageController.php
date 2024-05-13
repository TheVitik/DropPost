<?php

namespace App\Http\Controllers\ChannelSettings;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActiveAutomessageRequest;
use App\Http\Requests\UpdateAutomessageRequest;
use App\Models\Channel;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class AutoMessageController extends Controller
{
    /**
     * Activate or deactivate auto message for the channel.
     */
    public function active(ActiveAutomessageRequest $request, Project $project, Channel $channel): JsonResponse
    {
        $channel->update($request->validated());

        return response()->json(null, 204);
    }

    /**
     * Update auto message for the channel.
     */
    public function set(UpdateAutomessageRequest $request, Project $project, Channel $channel): JsonResponse
    {
        $channel->update($request->validated());

        return response()->json(null, 204);
    }
}
