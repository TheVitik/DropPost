<?php

namespace App\Http\Controllers\ChannelSettings;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActiveCopyPostRequest;
use App\Http\Requests\UpdateCopyPostRequest;
use App\Models\Channel;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class CopyPostController extends Controller
{
    /**
     * Activate or deactivate auto message for the channel.
     */
    public function active(ActiveCopyPostRequest $request, Project $project, Channel $channel): JsonResponse
    {
        $channel->update($request->validated());

        return response()->json(null, 204);
    }

    /**
     * Update auto message for the channel.
     */
    public function set(UpdateCopyPostRequest $request, Project $project, Channel $channel): JsonResponse
    {
        $channel->update($request->validated());

        return response()->json(null, 204);
    }
}
