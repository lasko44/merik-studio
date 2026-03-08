<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Models\Page;
use App\Models\PageComponent;
use App\Models\Product;
use App\Models\SiteSettings;
use App\Services\ProductCategoryService;
use App\Services\ProductService;
use App\Services\StripeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles CRUD operations for products in the admin panel.
 */
class ProductController extends Controller
{
    /**
     * Display a listing of all products.
     */
    public function index(ProductService $productService, StripeService $stripeService): Response
    {
        $this->authorize('viewAny', Product::class);

        $products = Product::select([
            'id',
            'name',
            'slug',
            'price',
            'currency',
            'image',
            'category_id',
            'sync_to_stripe',
            'stripe_product_id',
            'is_active',
            'is_featured',
            'type',
            'track_inventory',
            'quantity',
            'created_by',
            'updated_at',
        ])
            ->with(['creator:id,name', 'category:id,name,slug'])
            ->orderBy('name')
            ->get();

        $settings = SiteSettings::current();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'stats' => $productService->getStats(),
            'stripeConfigured' => $stripeService->isConfigured(),
            'productSettings' => [
                'showInNav' => $settings->products_show_in_nav ?? false,
                'navLabel' => $settings->products_nav_label ?? 'Products',
                'enableSearch' => $settings->products_enable_search ?? true,
                'enableCategoryFilter' => $settings->products_enable_category_filter ?? true,
            ],
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(StripeService $stripeService, ProductCategoryService $categoryService): Response
    {
        $this->authorize('create', Product::class);

        $components = PageComponent::groupedByCategory('product');
        $availablePages = Page::select(['id', 'title', 'path'])->where('is_published', true)->get();

        return Inertia::render('Admin/Products/Create', [
            'stripeConfigured' => $stripeService->isConfigured(),
            'categories' => $categoryService->getAllForSelect(),
            'components' => $components,
            'availablePages' => $availablePages,
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request, ProductService $service): RedirectResponse
    {
        $validated = $request->validated();
        $publish = $validated['publish'] ?? false;
        unset($validated['publish']);

        $product = $service->create($request->user(), $validated, $publish);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product for editing.
     */
    public function show(Product $product): RedirectResponse
    {
        return redirect()->route('admin.products.edit', $product);
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product, StripeService $stripeService, ProductCategoryService $categoryService): Response
    {
        $this->authorize('update', $product);

        $product->load(['creator:id,name', 'updater:id,name', 'category:id,name,slug']);

        $components = PageComponent::groupedByCategory('product');
        $availablePages = Page::select(['id', 'title', 'path'])->where('is_published', true)->get();

        return Inertia::render('Admin/Products/Edit', [
            'product' => $product,
            'stripeConfigured' => $stripeService->isConfigured(),
            'categories' => $categoryService->getAllForSelect(),
            'components' => $components,
            'availablePages' => $availablePages,
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product, ProductService $service): RedirectResponse
    {
        $validated = $request->validated();
        $publish = $validated['publish'] ?? false;
        unset($validated['publish']);

        $service->update($request->user(), $product, $validated, $publish);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product, ProductService $service): RedirectResponse
    {
        $this->authorize('delete', $product);

        $service->delete($product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Update product settings.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', Product::class);

        $validated = $request->validate([
            'showInNav' => 'boolean',
            'navLabel' => 'required|string|max:50',
            'enableSearch' => 'boolean',
            'enableCategoryFilter' => 'boolean',
        ]);

        $settings = SiteSettings::first();
        $settings->update([
            'products_show_in_nav' => $validated['showInNav'] ?? false,
            'products_nav_label' => $validated['navLabel'],
            'products_enable_search' => $validated['enableSearch'] ?? true,
            'products_enable_category_filter' => $validated['enableCategoryFilter'] ?? true,
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product settings updated successfully.');
    }
}
