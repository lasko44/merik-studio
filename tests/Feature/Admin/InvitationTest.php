<?php

use App\Mail\UserInvitationMail;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create permissions
    Permission::create(['name' => 'users.view']);
    Permission::create(['name' => 'users.create']);
    Permission::create(['name' => 'users.update']);
    Permission::create(['name' => 'users.delete']);

    // Create roles
    $adminRole = Role::create(['name' => 'Admin']);
    $adminRole->givePermissionTo(['users.view', 'users.create', 'users.update', 'users.delete']);

    $userRole = Role::create(['name' => 'User']);
    $userRole->givePermissionTo(['users.view']);
});

describe('store invitation', function () {
    it('allows admin to send invitation', function () {
        Mail::fake();

        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)
            ->post(route('admin.users.invite'), [
                'email' => 'newuser@example.com',
                'role' => 'User',
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('user_invitations', [
            'email' => 'newuser@example.com',
            'role' => 'User',
            'invited_by' => $admin->id,
            'status' => 'pending',
        ]);

        Mail::assertQueued(UserInvitationMail::class);
    });

    it('requires authentication', function () {
        $response = $this->post(route('admin.users.invite'), [
            'email' => 'newuser@example.com',
            'role' => 'User',
        ]);

        $response->assertRedirect(route('login'));
    });

    it('requires users.create permission', function () {
        $user = User::factory()->create();
        $user->assignRole('User'); // User role doesn't have users.create

        $response = $this->actingAs($user)
            ->post(route('admin.users.invite'), [
                'email' => 'newuser@example.com',
                'role' => 'User',
            ]);

        $response->assertStatus(403);
    });

    it('validates email is required', function () {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)
            ->post(route('admin.users.invite'), [
                'role' => 'User',
            ]);

        $response->assertSessionHasErrors('email');
    });

    it('validates email format', function () {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)
            ->post(route('admin.users.invite'), [
                'email' => 'not-an-email',
                'role' => 'User',
            ]);

        $response->assertSessionHasErrors('email');
    });

    it('prevents inviting existing user', function () {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($admin)
            ->post(route('admin.users.invite'), [
                'email' => 'existing@example.com',
                'role' => 'User',
            ]);

        $response->assertSessionHasErrors('email');
    });

    it('prevents duplicate pending invitation', function () {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        UserInvitation::factory()->create([
            'email' => 'pending@example.com',
            'status' => 'pending',
            'invited_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.users.invite'), [
                'email' => 'pending@example.com',
                'role' => 'User',
            ]);

        $response->assertSessionHasErrors('email');
    });

    it('validates role is required', function () {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)
            ->post(route('admin.users.invite'), [
                'email' => 'newuser@example.com',
            ]);

        $response->assertSessionHasErrors('role');
    });

    it('validates role exists', function () {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)
            ->post(route('admin.users.invite'), [
                'email' => 'newuser@example.com',
                'role' => 'NonExistentRole',
            ]);

        $response->assertSessionHasErrors('role');
    });
});

describe('destroy invitation', function () {
    it('allows admin to revoke invitation', function () {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $invitation = UserInvitation::factory()->create([
            'invited_by' => $admin->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.users.invitations.destroy', $invitation));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        expect($invitation->fresh()->status)->toBe('revoked');
    });

    it('requires authentication', function () {
        $invitation = UserInvitation::factory()->create();

        $response = $this->delete(route('admin.users.invitations.destroy', $invitation));

        $response->assertRedirect(route('login'));
    });

    it('requires users.create permission', function () {
        $user = User::factory()->create();
        $user->assignRole('User');

        $invitation = UserInvitation::factory()->create();

        $response = $this->actingAs($user)
            ->delete(route('admin.users.invitations.destroy', $invitation));

        $response->assertStatus(403);
    });

    it('cannot revoke already accepted invitation', function () {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $invitation = UserInvitation::factory()->accepted()->create([
            'invited_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.users.invitations.destroy', $invitation));

        $response->assertStatus(403);
    });
});

describe('resend invitation', function () {
    it('allows admin to resend invitation', function () {
        Mail::fake();

        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $invitation = UserInvitation::factory()->create([
            'invited_by' => $admin->id,
            'status' => 'pending',
        ]);

        $oldToken = $invitation->token;

        $response = $this->actingAs($admin)
            ->post(route('admin.users.invitations.resend', $invitation));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        // Token was regenerated
        expect($invitation->fresh()->token)->not->toBe($oldToken);

        Mail::assertQueued(UserInvitationMail::class);
    });

    it('requires authentication', function () {
        $invitation = UserInvitation::factory()->create();

        $response = $this->post(route('admin.users.invitations.resend', $invitation));

        $response->assertRedirect(route('login'));
    });

    it('requires users.create permission', function () {
        $user = User::factory()->create();
        $user->assignRole('User');

        $invitation = UserInvitation::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('admin.users.invitations.resend', $invitation));

        $response->assertStatus(403);
    });

    it('cannot resend already accepted invitation', function () {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $invitation = UserInvitation::factory()->accepted()->create([
            'invited_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.users.invitations.resend', $invitation));

        $response->assertStatus(403);
    });
});
