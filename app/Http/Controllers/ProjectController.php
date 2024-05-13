<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\InviteUserRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectWithRoleResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects.
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $projects = $user->userProjects;

        return response()->json(ProjectWithRoleResource::collection($projects));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $project = Project::create($request->validated());
            $project->users()->attach(auth()->id());
            return response()->json(new ProjectResource($project), 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): JsonResponse
    {
        return response()->json(new ProjectResource($project));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project->update($request->validated());

        return response()->json(new ProjectResource($project));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return response()->json(null, 204);
    }
}
