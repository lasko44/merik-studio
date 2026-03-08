<?php

namespace App\Services;

use App\Models\Page;
use App\Models\SiteSettings;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Handles page creation, updates, and lifecycle management.
 */
class PageService
{
    /**
     * Create a new page.
     */
    public function create(User $user, array $data): Page
    {
        $slug = Arr::get($data, 'slug') ?: Page::generateSlug(Arr::get($data, 'title'));
        $path = Arr::get($data, 'path') ?: '/' . $slug;

        // Get content arrays
        $content = Arr::get($data, 'content', []);
        $sidebarContent = Arr::get($data, 'sidebar_content', []);

        return Page::create([
            'title' => Arr::get($data, 'title'),
            'slug' => $slug,
            'path' => $this->normalizePath($path),
            'meta_description' => Arr::get($data, 'meta_description'),
            'meta_keywords' => Arr::get($data, 'meta_keywords'),
            'og_title' => Arr::get($data, 'og_title'),
            'og_description' => Arr::get($data, 'og_description'),
            'og_image' => Arr::get($data, 'og_image'),
            'twitter_card_type' => Arr::get($data, 'twitter_card_type', 'summary_large_image'),
            'canonical_url' => Arr::get($data, 'canonical_url'),
            'no_index' => Arr::get($data, 'no_index', false),
            'no_follow' => Arr::get($data, 'no_follow', false),
            'schema_type' => Arr::get($data, 'schema_type', 'WebPage'),
            'faqs' => Arr::get($data, 'faqs'),
            'speakable_selectors' => Arr::get($data, 'speakable_selectors'),
            'priority' => Arr::get($data, 'priority', 0.5),
            'change_frequency' => Arr::get($data, 'change_frequency', 'weekly'),
            'content' => $content,
            'draft_content' => $content, // Also populate draft for editor
            'sidebar_content' => $sidebarContent,
            'draft_sidebar_content' => $sidebarContent, // Also populate draft for editor
            'template' => Arr::get($data, 'template', 'default'),
            'template_options' => Arr::get($data, 'template_options'),
            'is_published' => Arr::get($data, 'is_published', false),
            'is_admin_page' => Arr::get($data, 'is_admin_page', false),
            'show_in_nav' => Arr::get($data, 'show_in_nav', true),
            'nav_order' => Arr::get($data, 'nav_order', 0),
            'sort_order' => Arr::get($data, 'sort_order', 0),
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    /**
     * Update an existing page (saves to draft by default).
     */
    public function update(User $user, Page $page, array $data, bool $publish = false): Page
    {
        $updateData = [
            'updated_by' => $user->id,
        ];

        $allowedFields = [
            'title',
            'slug',
            'meta_description',
            'meta_keywords',
            'og_title',
            'og_description',
            'og_image',
            'twitter_card_type',
            'canonical_url',
            'no_index',
            'no_follow',
            'schema_type',
            'faqs',
            'speakable_selectors',
            'priority',
            'change_frequency',
            'template',
            'template_options',
            'show_in_nav',
            'sort_order',
        ];

        foreach ($allowedFields as $field) {
            if (Arr::has($data, $field)) {
                $updateData[$field] = Arr::get($data, $field);
            }
        }

        if (Arr::has($data, 'path')) {
            $updateData['path'] = $this->normalizePath(Arr::get($data, 'path'));
        }

        // Always save content to draft fields
        if (Arr::has($data, 'content')) {
            $updateData['draft_content'] = Arr::get($data, 'content');
        }
        if (Arr::has($data, 'sidebar_content')) {
            $updateData['draft_sidebar_content'] = Arr::get($data, 'sidebar_content');
        }

        if ($publish) {
            // Copy draft content to published content
            $updateData['content'] = Arr::get($data, 'content', $page->draft_content);
            $updateData['sidebar_content'] = Arr::get($data, 'sidebar_content', $page->draft_sidebar_content);
            $updateData['has_unpublished_changes'] = false;

            // Also mark as published if requested
            if (Arr::has($data, 'is_published')) {
                $updateData['is_published'] = Arr::get($data, 'is_published');
            }
        } else {
            // Mark that there are unpublished changes
            $updateData['has_unpublished_changes'] = true;
        }

        $page->update($updateData);

        return $page->fresh();
    }

    /**
     * Soft delete a page.
     */
    public function delete(Page $page): bool
    {
        return $page->delete();
    }

    /**
     * Force delete a page (Super Admin only).
     */
    public function forceDelete(Page $page): bool
    {
        return $page->forceDelete();
    }

    /**
     * Restore a soft-deleted page.
     */
    public function restore(Page $page): bool
    {
        return $page->restore();
    }

    /**
     * Publish a page.
     */
    public function publish(User $user, Page $page): Page
    {
        $page->update([
            'is_published' => true,
            'updated_by' => $user->id,
        ]);

        return $page->fresh();
    }

    /**
     * Unpublish a page.
     */
    public function unpublish(User $user, Page $page): Page
    {
        $page->update([
            'is_published' => false,
            'updated_by' => $user->id,
        ]);

        return $page->fresh();
    }

    /**
     * Duplicate a page.
     */
    public function duplicate(User $user, Page $page): Page
    {
        $newTitle = $page->title . ' (Copy)';
        $newSlug = Page::generateSlug($newTitle);

        return Page::create([
            'title' => $newTitle,
            'slug' => $newSlug,
            'path' => '/' . $newSlug,
            'meta_description' => $page->meta_description,
            'meta_keywords' => $page->meta_keywords,
            'content' => $page->content,
            'template' => $page->template,
            'template_options' => $page->template_options,
            'is_published' => false,
            'is_admin_page' => $page->is_admin_page,
            'sort_order' => $page->sort_order,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    /**
     * Reorder pages.
     */
    public function reorder(array $pageIds): void
    {
        foreach ($pageIds as $index => $pageId) {
            Page::where('id', $pageId)->update(['sort_order' => $index]);
        }
    }

    /**
     * Find a page by its path.
     */
    public function findByPath(string $path): ?Page
    {
        return Page::where('path', $this->normalizePath($path))
            ->published()
            ->first();
    }

    /**
     * Normalize a path to ensure consistent format.
     */
    private function normalizePath(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        $path = rtrim($path, '/');
        $path = preg_replace('/\/+/', '/', $path);

        return $path ?: '/';
    }

    /**
     * Check if a path is valid (not reserved).
     */
    public function isValidPath(string $path): bool
    {
        $reserved = [
            '/admin',
            '/api',
            '/login',
            '/register',
            '/logout',
            '/password',
            '/email',
            '/dashboard',
        ];

        $normalizedPath = $this->normalizePath($path);

        foreach ($reserved as $reservedPath) {
            if (Str::startsWith($normalizedPath, $reservedPath)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get navigation items from published pages.
     */
    public function getNavigation(int $limit = 10): array
    {
        // Get all nav-eligible pages with their hierarchy
        $pages = Page::select(['id', 'title', 'path', 'parent_id', 'nav_order'])
            ->published()
            ->where('show_in_nav', true)
            ->where('path', '!=', '/')
            ->where('path', 'not like', '/blog/%')
            ->where('path', 'not like', '/products/%')
            ->orderBy('nav_order')
            ->get();

        $navigation = $this->buildNavigationTree($pages, null, $limit);

        // Collect special items (blog, products) to insert at correct positions
        $settings = SiteSettings::current();
        $specialItems = [];

        if ($settings->blog_show_in_nav) {
            $specialItems[] = [
                'title' => $settings->blog_nav_label ?? 'Blog',
                'path' => '/blog',
                'children' => [],
                'nav_order' => $settings->blog_nav_order ?? 999,
            ];
        }

        if ($settings->products_show_in_nav) {
            $specialItems[] = [
                'title' => $settings->products_nav_label ?? 'Products',
                'path' => '/products',
                'children' => [],
                'nav_order' => $settings->products_nav_order ?? 999,
            ];
        }

        // Sort special items by nav_order
        usort($specialItems, fn($a, $b) => $a['nav_order'] <=> $b['nav_order']);

        // Insert special items at the correct positions
        foreach ($specialItems as $specialItem) {
            $navOrder = $specialItem['nav_order'];
            unset($specialItem['nav_order']); // Remove nav_order from final output

            if ($navOrder >= count($navigation)) {
                $navigation[] = $specialItem;
            } else {
                array_splice($navigation, $navOrder, 0, [$specialItem]);
            }
        }

        return $navigation;
    }

    /**
     * Build nested navigation tree from flat collection.
     */
    protected function buildNavigationTree($pages, ?int $parentId, int $limit, int &$count = 0): array
    {
        $tree = [];

        foreach ($pages->where('parent_id', $parentId) as $page) {
            if ($count >= $limit) {
                break;
            }

            $count++;
            $children = $this->buildNavigationTree($pages, $page->id, $limit, $count);

            $tree[] = [
                'title' => $page->title,
                'path' => $page->path,
                'children' => $children,
            ];
        }

        return $tree;
    }

    /**
     * Get all pages for admin listing with eager-loaded relationships.
     */
    public function getAllForAdmin(): \Illuminate\Database\Eloquent\Collection
    {
        return Page::select([
            'id',
            'title',
            'slug',
            'path',
            'is_published',
            'is_admin_page',
            'sort_order',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ])
            ->with(['creator:id,name', 'updater:id,name'])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();
    }

    /**
     * Get available components grouped by category for page builder.
     */
    public function getAvailableComponents(): array
    {
        return \App\Models\PageComponent::groupedByCategory();
    }

    /**
     * Get available pages for link selection dropdowns.
     *
     * @return array<int, array{id: int, title: string, path: string}>
     */
    public function getAvailablePagesForLinks(): array
    {
        return Page::select(['id', 'title', 'path'])
            ->where('is_published', true)
            ->orderBy('title')
            ->get()
            ->map(fn (Page $page) => [
                'id' => $page->id,
                'title' => $page->title,
                'path' => $page->path,
            ])
            ->toArray();
    }
}
