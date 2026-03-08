<?php

namespace App\Services;

use App\Models\Page;
use App\Models\SiteSettings;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Handles navigation structure management and tree building.
 */
class NavigationService
{
    /**
     * Get data for the navigation editor.
     */
    public function getEditorData(): array
    {
        $pages = Page::select([
            'id',
            'title',
            'path',
            'is_published',
            'show_in_nav',
            'parent_id',
            'nav_order',
        ])
            ->where('is_admin_page', false)
            ->orderBy('nav_order')
            ->orderBy('title')
            ->get();

        $settings = SiteSettings::current();
        $navItems = $this->buildNavTree($pages->where('show_in_nav', true));

        // Collect special items (blog, products) to insert at correct positions
        $specialItems = [];

        if ($settings->blog_show_in_nav) {
            $specialItems[] = [
                'id' => 'blog',
                'title' => $settings->blog_nav_label ?? 'Blog',
                'path' => '/blog',
                'is_published' => true,
                'show_in_nav' => true,
                'parent_id' => null,
                'nav_order' => $settings->blog_nav_order ?? 999,
                'children' => [],
                'is_blog' => true,
            ];
        }

        if ($settings->products_show_in_nav) {
            $specialItems[] = [
                'id' => 'products',
                'title' => $settings->products_nav_label ?? 'Products',
                'path' => '/products',
                'is_published' => true,
                'show_in_nav' => true,
                'parent_id' => null,
                'nav_order' => $settings->products_nav_order ?? 999,
                'children' => [],
                'is_products' => true,
            ];
        }

        // Insert special items at the correct positions based on nav_order
        foreach ($specialItems as $specialItem) {
            $inserted = false;
            for ($i = 0; $i < count($navItems); $i++) {
                if ($navItems[$i]['nav_order'] > $specialItem['nav_order']) {
                    array_splice($navItems, $i, 0, [$specialItem]);
                    $inserted = true;
                    break;
                }
            }
            if (!$inserted) {
                $navItems[] = $specialItem;
            }
        }

        return [
            'navItems' => $navItems,
            'availablePages' => $pages->where('show_in_nav', false)->values(),
            'blogSettings' => [
                'showInNav' => $settings->blog_show_in_nav,
                'navLabel' => $settings->blog_nav_label ?? 'Blog',
                'navOrder' => $settings->blog_nav_order ?? 999,
            ],
            'productSettings' => [
                'showInNav' => $settings->products_show_in_nav,
                'navLabel' => $settings->products_nav_label ?? 'Products',
                'navOrder' => $settings->products_nav_order ?? 999,
            ],
        ];
    }

    /**
     * Update the navigation structure from editor data.
     */
    public function updateStructure(array $data): void
    {
        $blogItem = null;
        $productsItem = null;

        foreach (Arr::get($data, 'items', []) as $item) {
            $itemId = Arr::get($item, 'id');

            // Handle blog item separately
            if ($itemId === 'blog') {
                $blogItem = $item;
                continue;
            }

            // Handle products item separately
            if ($itemId === 'products') {
                $productsItem = $item;
                continue;
            }

            Page::where('id', $itemId)->update([
                'parent_id' => Arr::get($item, 'parent_id'),
                'nav_order' => Arr::get($item, 'nav_order'),
                'show_in_nav' => Arr::get($item, 'show_in_nav'),
            ]);
        }

        // Update special nav settings
        // If blog/products are not in items, they were removed from nav
        $settingsUpdate = [
            'blog_show_in_nav' => $blogItem !== null ? Arr::get($blogItem, 'show_in_nav', false) : false,
            'blog_nav_order' => $blogItem !== null ? Arr::get($blogItem, 'nav_order', 999) : 999,
            'products_show_in_nav' => $productsItem !== null ? Arr::get($productsItem, 'show_in_nav', false) : false,
            'products_nav_order' => $productsItem !== null ? Arr::get($productsItem, 'nav_order', 999) : 999,
        ];

        $settings = SiteSettings::first();
        $settings->update($settingsUpdate);
    }

    /**
     * Build a nested tree structure from flat pages collection.
     */
    public function buildNavTree(Collection $pages, ?int $parentId = null): array
    {
        $tree = [];

        foreach ($pages->where('parent_id', $parentId) as $page) {
            $children = $this->buildNavTree($pages, $page->id);

            $tree[] = [
                'id' => $page->id,
                'title' => $page->title,
                'path' => $page->path,
                'is_published' => $page->is_published,
                'show_in_nav' => $page->show_in_nav,
                'parent_id' => $page->parent_id,
                'nav_order' => $page->nav_order,
                'children' => $children,
            ];
        }

        return $tree;
    }
}
