<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Represents a category for organizing help documentation articles.
 */
class HelpCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'sort_order',
    ];

    /**
     * Get the articles in this category.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(HelpArticle::class)->orderBy('sort_order');
    }

    /**
     * Get published articles in this category.
     */
    public function publishedArticles(): HasMany
    {
        return $this->articles()->where('is_published', true);
    }

    /**
     * Scope to order by sort order.
     */
    public function scopeOrdered(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy('sort_order');
    }
}
