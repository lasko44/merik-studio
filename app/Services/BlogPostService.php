<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Support\Arr;

/**
 * Handles blog post creation, updates, and lifecycle management.
 */
class BlogPostService
{
    /**
     * Create a new blog post.
     */
    public function create(User $user, array $data): BlogPost
    {
        $slug = Arr::get($data, 'slug') ?: BlogPost::generateSlug(Arr::get($data, 'title'));

        return BlogPost::create([
            'title' => Arr::get($data, 'title'),
            'slug' => $slug,
            'excerpt' => Arr::get($data, 'excerpt'),
            'featured_image' => Arr::get($data, 'featured_image'),
            'category_id' => Arr::get($data, 'category_id'),
            'content' => Arr::get($data, 'content', []),
            'meta_description' => Arr::get($data, 'meta_description'),
            'og_title' => Arr::get($data, 'og_title'),
            'og_description' => Arr::get($data, 'og_description'),
            'og_image' => Arr::get($data, 'og_image'),
            'no_index' => Arr::get($data, 'no_index', false),
            'is_published' => Arr::get($data, 'is_published', false),
            'author_id' => Arr::get($data, 'author_id', $user->id),
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    /**
     * Update an existing blog post (saves to draft by default).
     */
    public function update(User $user, BlogPost $post, array $data, bool $publish = false): BlogPost
    {
        $updateData = [
            'updated_by' => $user->id,
        ];

        $allowedFields = [
            'title',
            'slug',
            'excerpt',
            'featured_image',
            'category_id',
            'meta_description',
            'og_title',
            'og_description',
            'og_image',
            'no_index',
            'author_id',
        ];

        foreach ($allowedFields as $field) {
            if (Arr::has($data, $field)) {
                $updateData[$field] = Arr::get($data, $field);
            }
        }

        // Always save content to draft fields
        if (Arr::has($data, 'content')) {
            $updateData['draft_content'] = Arr::get($data, 'content');
        }

        if ($publish) {
            // Copy draft content to published content
            $updateData['content'] = Arr::get($data, 'content', $post->draft_content);
            $updateData['has_unpublished_changes'] = false;

            // Set published_at if not already set
            if (!$post->published_at) {
                $updateData['published_at'] = now();
            }

            // Also mark as published if requested
            if (Arr::has($data, 'is_published')) {
                $updateData['is_published'] = Arr::get($data, 'is_published');
            }
        } else {
            // Mark that there are unpublished changes
            $updateData['has_unpublished_changes'] = true;
        }

        $post->update($updateData);

        return $post->fresh();
    }

    /**
     * Soft delete a blog post.
     */
    public function delete(BlogPost $post): bool
    {
        return $post->delete();
    }

    /**
     * Force delete a blog post (Super Admin only).
     */
    public function forceDelete(BlogPost $post): bool
    {
        return $post->forceDelete();
    }

    /**
     * Restore a soft-deleted blog post.
     */
    public function restore(BlogPost $post): bool
    {
        return $post->restore();
    }

    /**
     * Publish a blog post.
     */
    public function publish(User $user, BlogPost $post): BlogPost
    {
        $updateData = [
            'is_published' => true,
            'updated_by' => $user->id,
            'has_unpublished_changes' => false,
        ];

        // Set published_at if not already set
        if (!$post->published_at) {
            $updateData['published_at'] = now();
        }

        // Copy draft content to published content if there are unpublished changes
        if ($post->has_unpublished_changes && $post->draft_content) {
            $updateData['content'] = $post->draft_content;
        }

        $post->update($updateData);

        return $post->fresh();
    }

    /**
     * Unpublish a blog post.
     */
    public function unpublish(User $user, BlogPost $post): BlogPost
    {
        $post->update([
            'is_published' => false,
            'updated_by' => $user->id,
        ]);

        return $post->fresh();
    }

    /**
     * Duplicate a blog post.
     */
    public function duplicate(User $user, BlogPost $post): BlogPost
    {
        $newTitle = $post->title . ' (Copy)';
        $newSlug = BlogPost::generateSlug($newTitle);

        return BlogPost::create([
            'title' => $newTitle,
            'slug' => $newSlug,
            'excerpt' => $post->excerpt,
            'featured_image' => $post->featured_image,
            'category_id' => $post->category_id,
            'content' => $post->content,
            'meta_description' => $post->meta_description,
            'og_title' => $post->og_title,
            'og_description' => $post->og_description,
            'og_image' => $post->og_image,
            'no_index' => $post->no_index,
            'is_published' => false,
            'author_id' => $user->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    /**
     * Get all blog posts for admin listing with eager-loaded relationships.
     */
    public function getAllForAdmin(): \Illuminate\Database\Eloquent\Collection
    {
        return BlogPost::select([
            'id',
            'title',
            'slug',
            'excerpt',
            'featured_image',
            'category_id',
            'is_published',
            'published_at',
            'has_unpublished_changes',
            'author_id',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ])
            ->with(['category:id,name,slug', 'author:id,name', 'creator:id,name', 'updater:id,name'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get published blog posts for public listing with optional search and category filtering.
     */
    public function getPublishedPosts(int $perPage = 10, ?string $search = null, ?string $categorySlug = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = BlogPost::select([
            'id',
            'title',
            'slug',
            'excerpt',
            'featured_image',
            'category_id',
            'published_at',
            'author_id',
        ])
            ->with(['category:id,name,slug', 'author:id,name'])
            ->published();

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', '%' . $search . '%')
                    ->orWhere('excerpt', 'ilike', '%' . $search . '%');
            });
        }

        // Apply category filter
        if ($categorySlug) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        return $query->orderByDesc('published_at')->paginate($perPage);
    }

    /**
     * Get published posts by category.
     */
    public function getPostsByCategory(int $categoryId, int $perPage = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return BlogPost::select([
            'id',
            'title',
            'slug',
            'excerpt',
            'featured_image',
            'category_id',
            'published_at',
            'author_id',
        ])
            ->with(['category:id,name,slug', 'author:id,name'])
            ->where('category_id', $categoryId)
            ->published()
            ->orderByDesc('published_at')
            ->paginate($perPage);
    }

    /**
     * Find a published blog post by slug.
     */
    public function findPublishedBySlug(string $slug): ?BlogPost
    {
        return BlogPost::with(['category:id,name,slug', 'author:id,name'])
            ->where('slug', $slug)
            ->published()
            ->first();
    }
}
