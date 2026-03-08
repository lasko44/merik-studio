<?php

namespace App\Services;

use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Illuminate\Database\Eloquent\Collection;

/**
 * Manages help documentation retrieval and organization.
 */
class HelpService
{
    /**
     * Get all categories with their published articles.
     */
    public function getAllCategoriesWithArticles(): Collection
    {
        return HelpCategory::with(['publishedArticles'])
            ->ordered()
            ->get();
    }

    /**
     * Get a specific category by slug.
     */
    public function getCategoryBySlug(string $slug): ?HelpCategory
    {
        return HelpCategory::with(['publishedArticles'])
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Get a specific article by slug.
     */
    public function getArticleBySlug(string $slug): ?HelpArticle
    {
        return HelpArticle::with(['category'])
            ->published()
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Search articles by keyword.
     */
    public function searchArticles(string $query): Collection
    {
        return HelpArticle::published()
            ->with(['category'])
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%")
                    ->orWhere('excerpt', 'like', "%{$query}%");
            })
            ->ordered()
            ->get();
    }

    /**
     * Get the table of contents for navigation.
     */
    public function getTableOfContents(): array
    {
        $categories = HelpCategory::with(['publishedArticles:id,help_category_id,title,slug'])
            ->ordered()
            ->get();

        return $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'icon' => $category->icon,
                'articles' => $category->publishedArticles->map(function ($article) {
                    return [
                        'id' => $article->id,
                        'title' => $article->title,
                        'slug' => $article->slug,
                    ];
                })->toArray(),
            ];
        })->toArray();
    }
}
