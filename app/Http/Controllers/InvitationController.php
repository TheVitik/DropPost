<?php

namespace App\Http\Controllers;

use App\Exceptions\AlreadyMemberInvitationException;
use App\Exceptions\InvalidUserInvitationException;
use App\Http\Resources\InvitationResource;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvitationController extends Controller
{
    /**
     * Show the invitation details.
     * @throws AlreadyMemberInvitationException
     * @throws InvalidUserInvitationException
     */
    public function showLink(Request $request, Invitation $invitation): JsonResponse
    {
        return response()->json(new InvitationResource($invitation));
    }

    /**
     * Accept the invitation.
     * @throws AlreadyMemberInvitationException
     * @throws InvalidUserInvitationException
     */
    public function acceptLink(Request $request, Invitation $invitation): JsonResponse
    {
        if ($invitation->isAccepted()) {
            return response()->json(['message' => 'Запрошення не знайдено або воно неактивне'], 404);
        }

        $user = $request->user();
        $this->checkUser($invitation, $user);

        return DB::transaction(function () use ($invitation, $request) {
            $invitation->accept();
            $invitation->project->users()->attach($request->user()->id, [
                'role' => 'member',
            ]);

            return response()->json(null, 204);
        });
    }

    /**
     * Decline an invitation.
     * @throws AlreadyMemberInvitationException
     * @throws InvalidUserInvitationException
     */
    public function declineLink(Request $request, Invitation $invitation): JsonResponse
    {
        if ($invitation->isAccepted()) {
            return response()->json(['message' => 'Запрошення не знайдено або воно неактивне'], 404);
        }

        $user = $request->user();
        $this->checkUser($invitation, $user);

        $invitation->decline();

        return response()->json(null, 204);
    }

    /**
     * Check if the user can manage the invitation.
     * @throws InvalidUserInvitationException
     * @throws AlreadyMemberInvitationException
     */
    private function checkUser(Invitation $invitation, User $user): void
    {
        // Invitation does not belong to the user
        if ($invitation->username && $user->username !== $invitation->username) {
            throw new InvalidUserInvitationException();
        } elseif ($invitation->telegram_user_id && $user->telegram_user_id !== $invitation->telegram_user_id) {
            throw new InvalidUserInvitationException();
        }

        // User is already a member of the project
        if ($user->isMemberOfProject($invitation->project)) {
            throw new AlreadyMemberInvitationException();
        }
    }
}
