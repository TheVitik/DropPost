<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\InviteUserRequest;
use App\Http\Resources\ProjectUserResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectUserController extends Controller
{
    public function index(Project $project): JsonResponse
    {
        return response()->json(ProjectUserResource::collection($project->users));
    }

    /**
     * Invite a user to a project.
     */
    public function invite(InviteUserRequest $request, Project $project): JsonResponse
    {
        $project->users()->attach($request->user_id, [
            'role' => UserRole::MEMBER,
            'is_invited' => true,
        ]);

        return response()->json(null, 201);
    }

    /**
     * Remove a user from a project.
     */
    public function remove(Request $request, Project $project): JsonResponse
    {
        $project->users()->detach($request->user_id);

        return response()->json(null, 204);
    }
}
