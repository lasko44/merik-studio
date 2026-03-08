<?php

use App\Mail\UserInvitationMail;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\InvitationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(InvitationService::class);

    // Create roles
    \Spatie\Permission\Models\Role::create(['name' => 'Admin']);
    \Spatie\Permission\Models\Role::create(['name' => 'User']);
});

describe('sendInvitation', function () {
    it('creates an invitation and sends email', function () {
        Mail::fake();

        $inviter = User::factory()->create();
        $email = 'newuser@example.com';
        $role = 'Admin';

        $invitation = $this->service->sendInvitation($inviter, $email, $role);

        expect($invitation)
            ->toBeInstanceOf(UserInvitation::class)
            ->email->toBe($email)
            ->role->toBe($role)
            ->status->toBe('pending')
            ->invited_by->toBe($inviter->id);

        expect($invitation->token)->toHaveLength(64);
        expect($invitation->expires_at)->toBeGreaterThan(now());

        Mail::assertQueued(UserInvitationMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    });

    it('sets expiry based on config', function () {
        Mail::fake();
        config(['cms.invitation_expiry_days' => 14]);

        $inviter = User::factory()->create();
        $invitation = $this->service->sendInvitation($inviter, 'test@example.com', 'User');

        // Check that expiry is set to 14 days from now (within seconds tolerance)
        $expectedExpiry = now()->addDays(14);
        expect($invitation->expires_at->format('Y-m-d'))->toBe($expectedExpiry->format('Y-m-d'));
    });
});

describe('resendInvitation', function () {
    it('generates new token and extends expiry', function () {
        Mail::fake();

        $inviter = User::factory()->create();
        $invitation = UserInvitation::create([
            'email' => 'test@example.com',
            'token' => UserInvitation::generateToken(),
            'role' => 'User',
            'invited_by' => $inviter->id,
            'status' => 'pending',
            'expires_at' => now()->addDays(1),
        ]);

        $oldToken = $invitation->token;
        $oldExpiry = $invitation->expires_at;

        $updated = $this->service->resendInvitation($invitation);

        expect($updated->token)->not->toBe($oldToken);
        expect($updated->expires_at)->toBeGreaterThan($oldExpiry);

        Mail::assertQueued(UserInvitationMail::class);
    });
});

describe('revokeInvitation', function () {
    it('sets status to revoked', function () {
        $inviter = User::factory()->create();
        $invitation = UserInvitation::create([
            'email' => 'test@example.com',
            'token' => UserInvitation::generateToken(),
            'role' => 'User',
            'invited_by' => $inviter->id,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        $this->service->revokeInvitation($invitation);

        expect($invitation->fresh()->status)->toBe('revoked');
    });
});

describe('acceptInvitation', function () {
    it('creates user with correct role and marks invitation accepted', function () {
        $inviter = User::factory()->create();
        $invitation = UserInvitation::create([
            'email' => 'newuser@example.com',
            'token' => UserInvitation::generateToken(),
            'role' => 'Admin',
            'invited_by' => $inviter->id,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        $user = $this->service->acceptInvitation($invitation, [
            'name' => 'New User',
            'password' => 'password123',
        ]);

        expect($user)
            ->toBeInstanceOf(User::class)
            ->name->toBe('New User')
            ->email->toBe('newuser@example.com');

        expect($user->email_verified_at)->not->toBeNull();
        expect($user->hasRole('Admin'))->toBeTrue();

        expect($invitation->fresh())
            ->status->toBe('accepted')
            ->accepted_at->not->toBeNull();
    });
});

describe('findByToken', function () {
    it('returns invitation when token exists', function () {
        $inviter = User::factory()->create();
        $token = UserInvitation::generateToken();

        UserInvitation::create([
            'email' => 'test@example.com',
            'token' => $token,
            'role' => 'User',
            'invited_by' => $inviter->id,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        $found = $this->service->findByToken($token);

        expect($found)->not->toBeNull();
        expect($found->token)->toBe($token);
    });

    it('returns null when token does not exist', function () {
        $found = $this->service->findByToken('nonexistent-token');

        expect($found)->toBeNull();
    });
});

describe('getStatistics', function () {
    it('returns correct counts', function () {
        $inviter = User::factory()->create();

        // Create invitations with different statuses
        UserInvitation::factory()->count(3)->create([
            'invited_by' => $inviter->id,
            'status' => 'pending',
        ]);
        UserInvitation::factory()->count(2)->create([
            'invited_by' => $inviter->id,
            'status' => 'accepted',
        ]);
        UserInvitation::factory()->create([
            'invited_by' => $inviter->id,
            'status' => 'expired',
        ]);

        $stats = $this->service->getStatistics();

        expect($stats)
            ->pending->toBe(3)
            ->accepted->toBe(2)
            ->expired->toBe(1)
            ->revoked->toBe(0);
    });
});

describe('markExpiredInvitations', function () {
    it('marks expired pending invitations', function () {
        $inviter = User::factory()->create();

        // Create expired invitation
        UserInvitation::create([
            'email' => 'expired@example.com',
            'token' => UserInvitation::generateToken(),
            'role' => 'User',
            'invited_by' => $inviter->id,
            'status' => 'pending',
            'expires_at' => now()->subDay(),
        ]);

        // Create valid invitation
        UserInvitation::create([
            'email' => 'valid@example.com',
            'token' => UserInvitation::generateToken(),
            'role' => 'User',
            'invited_by' => $inviter->id,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        $count = $this->service->markExpiredInvitations();

        expect($count)->toBe(1);
        expect(UserInvitation::where('email', 'expired@example.com')->first()->status)->toBe('expired');
        expect(UserInvitation::where('email', 'valid@example.com')->first()->status)->toBe('pending');
    });
});
