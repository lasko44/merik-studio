<?php

namespace App\Services;

use App\Models\ProductCategory;
use Illuminate\Support\Arr;

/**
 * Handles product category management.
 */
class ProductCategoryService
{
    /**
     * Create a new product category.
     */
    public function create(array $data): ProductCategory
    {
        $slug = Arr::get($data, 'slug') ?: ProductCategory::generateSlug(Arr::get($data, 'name'));

        return ProductCategory::create([
            'name' => Arr::get($data, 'name'),
            'slug' => $slug,
            'description' => Arr::get($data, 'description'),
            'image' => Arr::get($data, 'image'),
            'is_featured' => Arr::get($data, 'is_featured', false),
            'sort_order' => Arr::get($data, 'sort_order', 0),
        ]);
    }

    /**
     * Update an existing product category.
     */
    public function update(ProductCategory $category, array $data): ProductCategory
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
     * Delete a product category.
     */
    public function delete(ProductCategory $category): bool
    {
        return $category->delete();
    }

    /**
     * Get all categories for admin listing.
     */
    public function getAllForAdmin(): \Illuminate\Database\Eloquent\Collection
    {
        return ProductCategory::select(['id', 'name', 'slug', 'description', 'image', 'is_featured', 'sort_order', 'created_at'])
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all categories for select dropdowns.
     */
    public function getAllForSelect(): \Illuminate\Database\Eloquent\Collection
    {
        return ProductCategory::select(['id', 'name', 'slug'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get categories with active products for public display.
     */
    public function getCategoriesWithProducts(): \Illuminate\Database\Eloquent\Collection
    {
        return ProductCategory::select(['id', 'name', 'slug', 'description', 'image', 'is_featured'])
            ->withCount(['activeProducts'])
            ->whereHas('activeProducts')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get featured categories for public display.
     */
    public function getFeaturedCategories(int $limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        return ProductCategory::select(['id', 'name', 'slug', 'description', 'image'])
            ->featured()
            ->whereHas('activeProducts')
            ->withCount('activeProducts')
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();
    }

    /**
     * Find a category by slug.
     */
    public function findBySlug(string $slug): ?ProductCategory
    {
        return ProductCategory::where('slug', $slug)->first();
    }
}
