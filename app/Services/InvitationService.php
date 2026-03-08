<?php

namespace App\Services;

use App\Mail\UserInvitationMail;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * Handles user invitation business logic.
 */
class InvitationService
{
    /**
     * Send a new invitation.
     */
    public function sendInvitation(User $inviter, string $email, string $role): UserInvitation
    {
        $expiryDays = config('cms.invitation_expiry_days', 7);

        $invitation = UserInvitation::create([
            'email' => $email,
            'token' => UserInvitation::generateToken(),
            'role' => $role,
            'invited_by' => $inviter->id,
            'status' => 'pending',
            'expires_at' => now()->addDays($expiryDays),
        ]);

        Mail::to($email)->send(new UserInvitationMail($invitation));

        return $invitation;
    }

    /**
     * Resend an existing invitation.
     */
    public function resendInvitation(UserInvitation $invitation): UserInvitation
    {
        $expiryDays = config('cms.invitation_expiry_days', 7);

        $invitation->update([
            'token' => UserInvitation::generateToken(),
            'expires_at' => now()->addDays($expiryDays),
            'status' => 'pending',
        ]);

        Mail::to($invitation->email)->send(new UserInvitationMail($invitation));

        return $invitation;
    }

    /**
     * Revoke an invitation.
     */
    public function revokeInvitation(UserInvitation $invitation): void
    {
        $invitation->revoke();
    }

    /**
     * Accept an invitation and create the user.
     */
    public function acceptInvitation(UserInvitation $invitation, array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $invitation->email,
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($invitation->role);

        $invitation->markAsAccepted();

        return $user;
    }

    /**
     * Find an invitation by token.
     */
    public function findByToken(string $token): ?UserInvitation
    {
        return UserInvitation::where('token', $token)->first();
    }

    /**
     * Get paginated invitations.
     */
    public function getPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return UserInvitation::with('inviter')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get pending invitations paginated.
     */
    public function getPendingPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return UserInvitation::with('inviter')
            ->pending()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get invitation statistics.
     */
    public function getStatistics(): array
    {
        return [
            'pending' => UserInvitation::pending()->count(),
            'accepted' => UserInvitation::where('status', 'accepted')->count(),
            'expired' => UserInvitation::where('status', 'expired')->count(),
            'revoked' => UserInvitation::where('status', 'revoked')->count(),
        ];
    }

    /**
     * Mark all expired invitations.
     */
    public function markExpiredInvitations(): int
    {
        return UserInvitation::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);
    }

    /**
     * Check if an email already has a pending invitation.
     */
    public function hasPendingInvitation(string $email): bool
    {
        return UserInvitation::where('email', $email)
            ->pending()
            ->exists();
    }
}
