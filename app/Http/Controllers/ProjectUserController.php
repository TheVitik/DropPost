<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\InviteUserRequest;
use App\Http\Resources\ProjectUserResource;
use App\Models\Invitation;
use App\Models\Project;
use App\Models\User;
use App\Services\Telegram\Client\InvitationService;
use danog\MadelineProto\Exception;
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
     * @throws Exception
     */
    public function invite(InviteUserRequest $request, Project $project): JsonResponse
    {
        $invitationService = new InvitationService($request->user()->phone);
        if ($request->has('username')) {
            $invitation = Invitation::create([
                'user_id' => $request->user()->id,
                'project_id' => $project->id,
                'username' => $request->username,
            ]);
            $invitationService->inviteByUsername($request->username, $project, $invitation);
        }

        $project->users()->attach($request->user_id, [
            'role' => UserRole::MEMBER,
            'is_invited' => true,
        ]);

        return response()->json(null, 201);
    }

    /**
     * Update a user's role and permissions in a project.
     */
    public function update(Request $request, Project $project, User $user): JsonResponse
    {
        $project->users()->updateExistingPivot($user->id, [
            'role' => $request->role,
            'permissions' => $request->permissions,
        ]);

        return response()->json(null, 204);
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
