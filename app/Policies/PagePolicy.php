<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;

/**
 * Authorization policy for Page model operations.
 */
class PagePolicy
{
    /**
     * Grant all abilities to super-admin.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('pages.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Page $page): bool
    {
        return $user->hasPermissionTo('pages.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if (!$user->hasPermissionTo('pages.create')) {
            return false;
        }

        return Page::canCreatePublicPage();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Page $page): bool
    {
        return $user->hasPermissionTo('pages.update');
    }

    /**
     * Determine whether the user can update navigation structure.
     */
    public function updateNavigation(User $user): bool
    {
        return $user->hasPermissionTo('pages.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Page $page): bool
    {
        return $user->hasPermissionTo('pages.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Page $page): bool
    {
        return $user->hasPermissionTo('pages.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Page $page): bool
    {
        return $user->hasPermissionTo('pages.force-delete');
    }
}
