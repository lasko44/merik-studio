<?php

use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create required roles
    Role::create(['name' => 'Admin']);
    Role::create(['name' => 'User']);
});

describe('show', function () {
    it('displays invitation acceptance page for valid token', function () {
        $inviter = User::factory()->create();
        $invitation = UserInvitation::factory()->create([
            'invited_by' => $inviter->id,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->get("/invitation/{$invitation->token}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Auth/AcceptInvitation')
            ->has('invitation')
            ->where('invitation.email', $invitation->email)
            ->where('invitation.token', $invitation->token)
        );
    });

    it('redirects to login for invalid token', function () {
        $response = $this->get('/invitation/invalid-token');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    });

    it('redirects to login for expired invitation', function () {
        $inviter = User::factory()->create();
        $invitation = UserInvitation::factory()->expired()->create([
            'invited_by' => $inviter->id,
        ]);

        $response = $this->get("/invitation/{$invitation->token}");

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    });

    it('redirects to login for already accepted invitation', function () {
        $inviter = User::factory()->create();
        $invitation = UserInvitation::factory()->accepted()->create([
            'invited_by' => $inviter->id,
        ]);

        $response = $this->get("/invitation/{$invitation->token}");

        $response->assertRedirect(route('login'));
    });

    it('redirects to login for revoked invitation', function () {
        $inviter = User::factory()->create();
        $invitation = UserInvitation::factory()->revoked()->create([
            'invited_by' => $inviter->id,
        ]);

        $response = $this->get("/invitation/{$invitation->token}");

        $response->assertRedirect(route('login'));
    });
});

describe('store', function () {
    it('creates user account and accepts invitation', function () {
        $inviter = User::factory()->create();
        $invitation = UserInvitation::factory()->create([
            'email' => 'newuser@example.com',
            'role' => 'Admin',
            'invited_by' => $inviter->id,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->post("/invitation/{$invitation->token}", [
            'name' => 'New User',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        // User was created
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'name' => 'New User',
        ]);

        // User has correct role
        $user = User::where('email', 'newuser@example.com')->first();
        expect($user->hasRole('Admin'))->toBeTrue();
        expect($user->email_verified_at)->not->toBeNull();

        // Invitation was accepted
        expect($invitation->fresh()->status)->toBe('accepted');

        // User is logged in
        $this->assertAuthenticatedAs($user);
    });

    it('requires name', function () {
        $inviter = User::factory()->create();
        $invitation = UserInvitation::factory()->create([
            'invited_by' => $inviter->id,
        ]);

        $response = $this->post("/invitation/{$invitation->token}", [
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
    });

    it('requires password', function () {
        $inviter = User::factory()->create();
        $invitation = UserInvitation::factory()->create([
            'invited_by' => $inviter->id,
        ]);

        $response = $this->post("/invitation/{$invitation->token}", [
            'name' => 'New User',
        ]);

        $response->assertSessionHasErrors('password');
    });

    it('requires password confirmation to match', function () {
        $inviter = User::factory()->create();
        $invitation = UserInvitation::factory()->create([
            'invited_by' => $inviter->id,
        ]);

        $response = $this->post("/invitation/{$invitation->token}", [
            'name' => 'New User',
            'password' => 'password123',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors('password');
    });

    it('requires minimum password length', function () {
        $inviter = User::factory()->create();
        $invitation = UserInvitation::factory()->create([
            'invited_by' => $inviter->id,
        ]);

        $response = $this->post("/invitation/{$invitation->token}", [
            'name' => 'New User',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    });

    it('rejects invalid token', function () {
        $response = $this->post('/invitation/invalid-token', [
            'name' => 'New User',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
    });

    it('rejects expired invitation', function () {
        $inviter = User::factory()->create();
        $invitation = UserInvitation::factory()->expired()->create([
            'invited_by' => $inviter->id,
        ]);

        $response = $this->post("/invitation/{$invitation->token}", [
            'name' => 'New User',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
    });
});
