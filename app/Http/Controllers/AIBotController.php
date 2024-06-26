<?php

namespace App\Http\Controllers;

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
    public function index(Project $project): JsonResponse
    {
        return response()->json(AIBotResource::collection($project->bots));
    }

    public function store(StoreAIBotRequest $request, Project $project): JsonResponse
    {
        $aiBot = $project->bots()->create($request->validated());

        return response()->json(new AIBotResource($aiBot), 201);
    }

    public function show(Project $project, AIBot $aiBot): JsonResponse
    {
        return response()->json(new AIBotResource($aiBot));
    }

    public function update(UpdateAIBotRequest $request, Project $project, AIBot $aiBot): JsonResponse
    {
        $aiBot->update($request->validated());

        return response()->json(new AIBotResource($aiBot));
    }

    public function destroy(Project $project, AIBot $aiBot): JsonResponse
    {
        $aiBot->delete();

        return response()->json(null, 204);
    }
}
