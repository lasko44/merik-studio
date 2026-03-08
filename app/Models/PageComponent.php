<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a reusable component for the page builder.
 */
class PageComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'icon',
        'vue_component',
        'default_props',
        'prop_schema',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'default_props' => 'array',
        'prop_schema' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Component categories.
     */
    public const CATEGORIES = [
        'layout' => 'Layout',
        'content' => 'Content',
        'media' => 'Media',
        'navigation' => 'Navigation',
        'forms' => 'Forms',
        'marketing' => 'Marketing',
        'commerce' => 'Commerce',
        'sidebar' => 'Sidebar',
        'product' => 'Product',
    ];

    /**
     * Scope to get only active components.
     */
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeInCategory(\Illuminate\Database\Eloquent\Builder $query, string $category): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Get components grouped by category.
     */
    public static function groupedByCategory(?string $onlyCategory = null): array
    {
        $query = static::active()->orderBy('category')->orderBy('sort_order');

        if ($onlyCategory !== null) {
            $query->where('category', $onlyCategory);
        }

        $components = $query->get();

        $grouped = [];

        $categories = $onlyCategory !== null
            ? [$onlyCategory => self::CATEGORIES[$onlyCategory] ?? ucfirst($onlyCategory)]
            : self::CATEGORIES;

        foreach ($categories as $key => $label) {
            $grouped[$key] = [
                'label' => $label,
                'components' => $components->where('category', $key)->values(),
            ];
        }

        return $grouped;
    }

    /**
     * Get the full Vue component path.
     */
    public function getComponentPath(): string
    {
        return "Components/PageBuilder/{$this->vue_component}";
    }
}
