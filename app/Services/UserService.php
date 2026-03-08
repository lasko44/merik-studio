<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Handles user management business logic.
 */
class UserService
{
    /**
     * Get paginated list of users.
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return User::with('roles')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Get all available roles.
     */
    public function getRoles(): Collection
    {
        return Role::orderBy('name')->get();
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if (isset($data['role'])) {
            $user->assignRole($data['role']);
        }

        return $user->load('roles');
    }

    /**
     * Update a user.
     */
    public function update(User $user, array $data): User
    {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if (isset($data['password']) && $data['password']) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user->fresh()->load('roles');
    }

    /**
     * Delete a user.
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Get user statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total' => User::count(),
            'admins' => User::role(['Super Admin', 'Admin'])->count(),
            'users' => User::role('User')->count(),
            'customers' => User::role('Customer')->count(),
        ];
    }
}
