<?php

namespace App\Services;

use App\Models\BlogCategory;
use Illuminate\Support\Arr;

/**
 * Handles blog category management.
 */
class BlogCategoryService
{
    /**
     * Create a new blog category.
     */
    public function create(array $data): BlogCategory
    {
        $slug = Arr::get($data, 'slug') ?: BlogCategory::generateSlug(Arr::get($data, 'name'));

        return BlogCategory::create([
            'name' => Arr::get($data, 'name'),
            'slug' => $slug,
            'description' => Arr::get($data, 'description'),
            'image' => Arr::get($data, 'image'),
            'is_featured' => Arr::get($data, 'is_featured', false),
            'sort_order' => Arr::get($data, 'sort_order', 0),
        ]);
    }

    /**
     * Update an existing blog category.
     */
    public function update(BlogCategory $category, array $data): BlogCategory
    {
        $updateData = [];

        $allowedFields = [
            'name',
            'slug',
            'description',
            'image',
            'is_featured',
            'sort_order',
        ];

        foreach ($allowedFields as $field) {
            if (Arr::has($data, $field)) {
                $updateData[$field] = Arr::get($data, $field);
            }
        }

        $category->update($updateData);

        return $category->fresh();
    }

    /**
     * Delete a blog category.
     */
    public function delete(BlogCategory $category): bool
    {
        return $category->delete();
    }

    /**
     * Get all categories for admin listing.
     */
    public function getAllForAdmin(): \Illuminate\Database\Eloquent\Collection
    {
        return BlogCategory::select(['id', 'name', 'slug', 'description', 'image', 'is_featured', 'sort_order', 'created_at'])
            ->withCount('posts')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all categories for select dropdowns.
     */
    public function getAllForSelect(): \Illuminate\Database\Eloquent\Collection
    {
        return BlogCategory::select(['id', 'name', 'slug'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get categories with published posts for public display.
     */
    public function getCategoriesWithPosts(): \Illuminate\Database\Eloquent\Collection
    {
        return BlogCategory::select(['id', 'name', 'slug', 'description'])
            ->withCount(['publishedPosts'])
            ->whereHas('publishedPosts')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get featured categories for public display.
     */
    public function getFeaturedCategories(int $limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        return BlogCategory::select(['id', 'name', 'slug', 'description', 'image'])
            ->featured()
            ->whereHas('publishedPosts')
            ->withCount('publishedPosts')
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();
    }

    /**
     * Find a category by slug.
     */
    public function findBySlug(string $slug): ?BlogCategory
    {
        return BlogCategory::where('slug', $slug)->first();
    }
}
