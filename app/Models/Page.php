<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Represents a page in the CMS that can be published publicly.
 */
class Page extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Boot the model and register event listeners.
     */
    protected static function boot(): void
    {
        parent::boot();

        // When a page is being deleted (soft delete), clean up navigation
        static::deleting(function (Page $page) {
            // Promote children to root level by removing parent_id
            static::where('parent_id', $page->id)->update(['parent_id' => null]);

            // Remove from navigation
            $page->show_in_nav = false;
            $page->saveQuietly();
        });

        // When a page is force deleted, also clean up
        static::forceDeleting(function (Page $page) {
            // Promote children to root level
            static::where('parent_id', $page->id)->update(['parent_id' => null]);
        });
    }

    protected $fillable = [
        'title',
        'slug',
        'path',
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
        'content',
        'sidebar_content',
        'draft_content',
        'draft_sidebar_content',
        'has_unpublished_changes',
        'template',
        'template_options',
        'is_published',
        'is_admin_page',
        'show_in_nav',
        'parent_id',
        'nav_order',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'content' => 'array',
        'sidebar_content' => 'array',
        'draft_content' => 'array',
        'draft_sidebar_content' => 'array',
        'has_unpublished_changes' => 'boolean',
        'template_options' => 'array',
        'faqs' => 'array',
        'speakable_selectors' => 'array',
        'is_published' => 'boolean',
        'is_admin_page' => 'boolean',
        'show_in_nav' => 'boolean',
        'no_index' => 'boolean',
        'no_follow' => 'boolean',
        'priority' => 'decimal:1',
    ];

    /**
     * Get the user who created this page.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this page.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the parent page for navigation nesting.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    /**
     * Get the child pages for navigation nesting.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id')->orderBy('nav_order');
    }

    /**
     * Get child pages that are visible in navigation.
     */
    public function navChildren(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id')
            ->where('show_in_nav', true)
            ->where('is_published', true)
            ->orderBy('nav_order');
    }

    /**
     * Scope to get only published public pages.
     */
    public function scopePublished(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_published', true)->where('is_admin_page', false);
    }

    /**
     * Scope to get only public pages (not admin pages).
     */
    public function scopePublic(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_admin_page', false);
    }

    /**
     * Scope to get only admin pages.
     */
    public function scopeAdmin(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_admin_page', true);
    }

    /**
     * Generate a unique slug from the title.
     */
    public static function generateSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        $query = static::withTrashed()->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $query = static::withTrashed()->where('slug', $slug);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            $counter++;
        }

        return $slug;
    }

    /**
     * Get the page limit based on the configured tier.
     */
    public static function getPageLimit(): ?int
    {
        $tier = config('features.page_limit_tier', 'basic');

        return match ($tier) {
            'basic' => 10,
            'standard' => 20,
            'unlimited' => null,
            default => 10,
        };
    }

    /**
     * Check if more public pages can be created.
     */
    public static function canCreatePublicPage(): bool
    {
        $limit = static::getPageLimit();

        if ($limit === null) {
            return true;
        }

        return static::public()->count() < $limit;
    }

    /**
     * Get the number of remaining public pages that can be created.
     */
    public static function remainingPublicPages(): ?int
    {
        $limit = static::getPageLimit();

        if ($limit === null) {
            return null;
        }

        return max(0, $limit - static::public()->count());
    }
}
