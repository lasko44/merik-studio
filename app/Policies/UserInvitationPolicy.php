<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserInvitation;

/**
 * Authorization policy for user invitations.
 */
class UserInvitationPolicy
{
    /**
     * Determine whether the user can view any invitations.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('users.view');
    }

    /**
     * Determine whether the user can create invitations.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('users.create');
    }

    /**
     * Determine whether the user can resend the invitation.
     */
    public function resend(User $user, UserInvitation $invitation): bool
    {
        if ($invitation->status !== 'pending') {
            return false;
        }

        return $user->hasPermissionTo('users.create');
    }

    /**
     * Determine whether the user can revoke the invitation.
     */
    public function revoke(User $user, UserInvitation $invitation): bool
    {
        if ($invitation->status !== 'pending') {
            return false;
        }

        return $user->hasPermissionTo('users.create');
    }
}
