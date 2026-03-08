<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Represents a product that can be sold, optionally synced to Stripe.
 */
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'image',
        'gallery_images',
        'content',
        'draft_content',
        'has_unpublished_changes',
        'category_id',
        'sync_to_stripe',
        'stripe_product_id',
        'stripe_price_id',
        'is_active',
        'is_featured',
        'type',
        'recurring_interval',
        'track_inventory',
        'quantity',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'gallery_images' => 'array',
        'content' => 'array',
        'draft_content' => 'array',
        'has_unpublished_changes' => 'boolean',
        'sync_to_stripe' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'track_inventory' => 'boolean',
        'quantity' => 'integer',
    ];

    /**
     * Get the category this product belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Get the user who created this product.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this product.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to get only active products.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only products synced to Stripe.
     */
    public function scopeSyncedToStripe(Builder $query): Builder
    {
        return $query->where('sync_to_stripe', true)
            ->whereNotNull('stripe_product_id');
    }

    /**
     * Scope to get one-time products.
     */
    public function scopeOneTime(Builder $query): Builder
    {
        return $query->where('type', 'one_time');
    }

    /**
     * Scope to get recurring products.
     */
    public function scopeRecurring(Builder $query): Builder
    {
        return $query->where('type', 'recurring');
    }

    /**
     * Scope to get featured products.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Check if the product is synced to Stripe.
     */
    public function isSyncedToStripe(): bool
    {
        return $this->sync_to_stripe && $this->stripe_product_id !== null;
    }

    /**
     * Check if the product is a recurring subscription.
     */
    public function isRecurring(): bool
    {
        return $this->type === 'recurring';
    }

    /**
     * Check if the product is in stock.
     */
    public function isInStock(): bool
    {
        if (!$this->track_inventory) {
            return true;
        }

        return $this->quantity > 0;
    }

    /**
     * Get the formatted price with currency.
     */
    public function getFormattedPriceAttribute(): string
    {
        $symbol = match ($this->currency) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $this->currency . ' ',
        };

        return $symbol . number_format((float) $this->price, 2);
    }

    /**
     * Generate a unique slug from the name.
     */
    public static function generateSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
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
}
