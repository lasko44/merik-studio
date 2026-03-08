<?php

namespace App\Policies;

use App\Models\User;

/**
 * Authorization policy for users.
 */
class UserPolicy
{
    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('users.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('users.view')
            || $user->id === $model->id;
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('users.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Users cannot update Super Admins unless they are Super Admin
        if ($model->hasRole('Super Admin') && !$user->hasRole('Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('users.update')
            || $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Cannot delete yourself
        if ($user->id === $model->id) {
            return false;
        }

        // Cannot delete Super Admins unless you're a Super Admin
        if ($model->hasRole('Super Admin') && !$user->hasRole('Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('users.delete');
    }
}
