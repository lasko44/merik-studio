<?php

namespace App\Policies;

use App\Models\BlogPost;
use App\Models\User;

/**
 * Authorization policy for BlogPost model operations.
 */
class BlogPostPolicy
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
        return $user->hasPermissionTo('blog.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BlogPost $post): bool
    {
        return $user->hasPermissionTo('blog.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('blog.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BlogPost $post): bool
    {
        return $user->hasPermissionTo('blog.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BlogPost $post): bool
    {
        return $user->hasPermissionTo('blog.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BlogPost $post): bool
    {
        return $user->hasPermissionTo('blog.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BlogPost $post): bool
    {
        return $user->hasPermissionTo('blog.force-delete');
    }
}
