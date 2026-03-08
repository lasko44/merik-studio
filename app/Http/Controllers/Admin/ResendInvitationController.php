<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserInvitation;
use App\Services\InvitationService;
use Illuminate\Http\RedirectResponse;

/**
 * Resends a user invitation email.
 */
class ResendInvitationController extends Controller
{
    /**
     * Resend an invitation.
     */
    public function __invoke(UserInvitation $invitation, InvitationService $service): RedirectResponse
    {
        $this->authorize('resend', $invitation);

        $service->resendInvitation($invitation);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Invitation resent successfully.');
    }
}
