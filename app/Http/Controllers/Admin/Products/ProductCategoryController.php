<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\StoreProductCategoryRequest;
use App\Http\Requests\Products\UpdateProductCategoryRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\ProductCategoryService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles CRUD operations for product categories in the admin panel.
 */
class ProductCategoryController extends Controller
{
    /**
     * Display a listing of all product categories.
     */
    public function index(ProductCategoryService $service): Response
    {
        $this->authorize('viewAny', Product::class);

        $categories = $service->getAllForAdmin();

        return Inertia::render('Admin/Products/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new product category.
     */
    public function create(): Response
    {
        $this->authorize('create', Product::class);

        return Inertia::render('Admin/Products/Categories/Create');
    }

    /**
     * Store a newly created product category.
     */
    public function store(StoreProductCategoryRequest $request, ProductCategoryService $service): RedirectResponse
    {
        $category = $service->create($request->validated());

        return redirect()
            ->route('admin.products.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified product category for editing.
     */
    public function show(ProductCategory $category): RedirectResponse
    {
        return redirect()->route('admin.products.categories.edit', $category);
    }

    /**
     * Show the form for editing the specified product category.
     */
    public function edit(ProductCategory $category): Response
    {
        $this->authorize('update', Product::class);

        return Inertia::render('Admin/Products/Categories/Edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified product category.
     */
    public function update(UpdateProductCategoryRequest $request, ProductCategory $category, ProductCategoryService $service): RedirectResponse
    {
        $service->update($category, $request->validated());

        return redirect()
            ->route('admin.products.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified product category.
     */
    public function destroy(ProductCategory $category, ProductCategoryService $service): RedirectResponse
    {
        $this->authorize('delete', Product::class);

        $service->delete($category);

        return redirect()
            ->route('admin.products.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
