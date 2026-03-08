<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Arr;

/**
 * Handles product creation, updates, and Stripe synchronization.
 */
class ProductService
{
    public function __construct(
        protected StripeService $stripeService
    ) {}

    /**
     * Create a new product.
     */
    public function create(User $user, array $data, bool $publish = false): Product
    {
        $slug = Arr::get($data, 'slug') ?: Product::generateSlug(Arr::get($data, 'name'));

        $contentData = [];
        if (Arr::has($data, 'content')) {
            if ($publish) {
                $contentData['content'] = Arr::get($data, 'content');
                $contentData['draft_content'] = null;
                $contentData['has_unpublished_changes'] = false;
            } else {
                $contentData['draft_content'] = Arr::get($data, 'content');
                $contentData['has_unpublished_changes'] = true;
            }
        }

        $product = Product::create(array_merge([
            'name' => Arr::get($data, 'name'),
            'slug' => $slug,
            'description' => Arr::get($data, 'description'),
            'price' => Arr::get($data, 'price'),
            'currency' => Arr::get($data, 'currency', 'USD'),
            'image' => Arr::get($data, 'image'),
            'category_id' => Arr::get($data, 'category_id'),
            'sync_to_stripe' => Arr::get($data, 'sync_to_stripe', false),
            'is_active' => Arr::get($data, 'is_active', true),
            'is_featured' => Arr::get($data, 'is_featured', false),
            'type' => Arr::get($data, 'type', 'one_time'),
            'recurring_interval' => Arr::get($data, 'recurring_interval'),
            'track_inventory' => Arr::get($data, 'track_inventory', false),
            'quantity' => Arr::get($data, 'quantity'),
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ], $contentData));

        // Sync to Stripe if enabled
        if ($product->sync_to_stripe && $this->stripeService->isConfigured()) {
            $this->syncToStripe($product);
        }

        return $product->fresh();
    }

    /**
     * Update an existing product.
     */
    public function update(User $user, Product $product, array $data, bool $publish = false): Product
    {
        $oldPrice = $product->price;
        $oldSyncToStripe = $product->sync_to_stripe;

        $updateData = [
            'updated_by' => $user->id,
        ];

        $allowedFields = [
            'name',
            'description',
            'price',
            'currency',
            'image',
            'category_id',
            'sync_to_stripe',
            'is_active',
            'is_featured',
            'type',
            'recurring_interval',
            'track_inventory',
            'quantity',
        ];

        foreach ($allowedFields as $field) {
            if (Arr::has($data, $field)) {
                $updateData[$field] = Arr::get($data, $field);
            }
        }

        // Handle slug separately to generate if empty
        if (Arr::has($data, 'slug')) {
            $slug = Arr::get($data, 'slug');
            $updateData['slug'] = $slug ?: Product::generateSlug(
                Arr::get($data, 'name', $product->name),
                $product->id
            );
        }

        // Handle content with draft/publish workflow
        if (Arr::has($data, 'content')) {
            if ($publish) {
                $updateData['content'] = Arr::get($data, 'content');
                $updateData['draft_content'] = null;
                $updateData['has_unpublished_changes'] = false;
            } else {
                $updateData['draft_content'] = Arr::get($data, 'content');
                $updateData['has_unpublished_changes'] = true;
            }
        }

        $product->update($updateData);
        $product->refresh();

        // Handle Stripe sync
        if ($this->stripeService->isConfigured()) {
            $syncEnabled = Arr::get($data, 'sync_to_stripe', $product->sync_to_stripe);
            $priceChanged = (float) $oldPrice !== (float) $product->price;

            if ($syncEnabled) {
                if (!$product->stripe_product_id) {
                    // Product not yet in Stripe, create it
                    $this->syncToStripe($product);
                } elseif ($priceChanged) {
                    // Price changed, create new Stripe price
                    $this->updateStripePrice($product);
                } else {
                    // Update Stripe product metadata
                    $this->updateStripeProduct($product);
                }
            }
        }

        return $product;
    }

    /**
     * Soft delete a product.
     */
    public function delete(Product $product): bool
    {
        // Archive in Stripe if synced
        if ($product->isSyncedToStripe() && $this->stripeService->isConfigured()) {
            $this->stripeService->archiveProduct($product->stripe_product_id);
        }

        return $product->delete();
    }

    /**
     * Sync a product to Stripe.
     */
    public function syncToStripe(Product $product): Product
    {
        if (!$this->stripeService->isConfigured()) {
            return $product;
        }

        // Create Stripe product
        $stripeProduct = $this->stripeService->createProduct($product);

        if ($stripeProduct) {
            $product->stripe_product_id = $stripeProduct->id;

            // Create Stripe price
            $stripePrice = $this->stripeService->createPrice($product);

            if ($stripePrice) {
                $product->stripe_price_id = $stripePrice->id;
            }

            $product->save();
        }

        return $product;
    }

    /**
     * Remove Stripe sync from a product (keeps Stripe product for reference).
     */
    public function unsyncFromStripe(Product $product): Product
    {
        $product->update([
            'sync_to_stripe' => false,
        ]);

        return $product;
    }

    /**
     * Update Stripe product metadata.
     */
    protected function updateStripeProduct(Product $product): void
    {
        if (!$product->stripe_product_id) {
            return;
        }

        $this->stripeService->updateProduct($product);
    }

    /**
     * Create a new Stripe price when price changes.
     */
    protected function updateStripePrice(Product $product): void
    {
        if (!$product->stripe_product_id) {
            return;
        }

        // Archive the old price
        if ($product->stripe_price_id) {
            $this->stripeService->archivePrice($product->stripe_price_id);
        }

        // Create new price
        $stripePrice = $this->stripeService->createPrice($product);

        if ($stripePrice) {
            $product->update([
                'stripe_price_id' => $stripePrice->id,
            ]);
        }
    }

    /**
     * Get product statistics for the admin dashboard.
     */
    public function getStats(): array
    {
        return [
            'total' => Product::count(),
            'active' => Product::active()->count(),
            'synced_to_stripe' => Product::syncedToStripe()->count(),
            'one_time' => Product::oneTime()->count(),
            'recurring' => Product::recurring()->count(),
        ];
    }

    /**
     * Get active products with optional filtering.
     */
    public function getActiveProducts(int $perPage = 12, ?string $search = null, ?string $categorySlug = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Product::select([
            'id',
            'name',
            'slug',
            'description',
            'price',
            'currency',
            'image',
            'category_id',
            'is_featured',
            'type',
            'recurring_interval',
        ])
            ->with('category:id,name,slug')
            ->active();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categorySlug) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        return $query->orderBy('is_featured', 'desc')
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get products by category.
     */
    public function getProductsByCategory(int $categoryId, int $perPage = 12): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Product::select([
            'id',
            'name',
            'slug',
            'description',
            'price',
            'currency',
            'image',
            'category_id',
            'is_featured',
            'type',
            'recurring_interval',
        ])
            ->with('category:id,name,slug')
            ->active()
            ->where('category_id', $categoryId)
            ->orderBy('is_featured', 'desc')
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get featured products.
     */
    public function getFeaturedProducts(int $limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        return Product::select([
            'id',
            'name',
            'slug',
            'description',
            'price',
            'currency',
            'image',
            'category_id',
            'is_featured',
            'type',
            'recurring_interval',
        ])
            ->with('category:id,name,slug')
            ->active()
            ->featured()
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    /**
     * Find a product by slug.
     */
    public function findBySlug(string $slug): ?Product
    {
        return Product::where('slug', $slug)
            ->active()
            ->with('category:id,name,slug')
            ->first();
    }
}
