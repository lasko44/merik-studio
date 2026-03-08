<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invitations\StoreInvitationRequest;
use App\Models\UserInvitation;
use App\Services\InvitationService;
use Illuminate\Http\RedirectResponse;

/**
 * Handles user invitation management.
 */
class InvitationController extends Controller
{
    /**
     * Store a new invitation.
     */
    public function store(StoreInvitationRequest $request, InvitationService $service): RedirectResponse
    {
        $service->sendInvitation(
            $request->user(),
            $request->validated('email'),
            $request->validated('role'),
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Invitation sent successfully.');
    }

    /**
     * Revoke an invitation.
     */
    public function destroy(UserInvitation $invitation, InvitationService $service): RedirectResponse
    {
        $this->authorize('revoke', $invitation);

        $service->revokeInvitation($invitation);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Invitation revoked successfully.');
    }
}
