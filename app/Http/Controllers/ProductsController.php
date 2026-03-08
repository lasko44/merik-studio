<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SiteSettings;
use App\Services\PageService;
use App\Services\ProductCategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Renders public-facing product pages.
 */
class ProductsController extends Controller
{
    /**
     * Display the products listing page.
     */
    public function index(
        Request $request,
        ProductService $productService,
        ProductCategoryService $categoryService,
        PageService $pageService
    ): Response {
        $search = $request->input('search');
        $categorySlug = $request->input('category');

        $products = $productService->getActiveProducts(12, $search, $categorySlug);
        $categories = $categoryService->getCategoriesWithProducts();
        $settings = SiteSettings::current();

        return Inertia::render('Public/Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'settings' => [
                'siteName' => $settings->site_name,
                'tagline' => $settings->tagline,
                'logoPath' => $settings->logo_path,
                'showSiteNameInNav' => $settings->show_site_name_in_nav ?? true,
                'navLogoHeight' => $settings->nav_logo_height ?? 32,
                'primaryColor' => $settings->primary_color,
                'secondaryColor' => $settings->secondary_color,
                'accentColor' => $settings->accent_color,
                'backgroundColor' => $settings->background_color,
                'textColor' => $settings->text_color,
                'successColor' => $settings->success_color,
                'warningColor' => $settings->warning_color,
                'errorColor' => $settings->error_color,
                'infoColor' => $settings->info_color,
                'surfaceColor' => $settings->surface_color,
                'borderColor' => $settings->border_color,
                'mutedColor' => $settings->muted_color,
                'contactEmail' => $settings->contact_email,
                'contactPhone' => $settings->contact_phone,
                'contactAddress' => $settings->contact_address,
                'socialLinks' => $settings->social_links,
            ],
            'productSettings' => [
                'enableSearch' => $settings->products_enable_search ?? true,
                'enableCategoryFilter' => $settings->products_enable_category_filter ?? true,
            ],
            'filters' => [
                'search' => $search,
                'category' => $categorySlug,
            ],
            'navigation' => $pageService->getNavigation(),
            'seo' => [
                'title' => 'Products | ' . $settings->site_name,
                'description' => $settings->default_meta_description,
            ],
        ]);
    }

    /**
     * Display products by category.
     */
    public function category(
        ProductCategory $category,
        ProductService $productService,
        ProductCategoryService $categoryService,
        PageService $pageService
    ): Response {
        $products = $productService->getProductsByCategory($category->id, 12);
        $categories = $categoryService->getCategoriesWithProducts();
        $settings = SiteSettings::current();

        return Inertia::render('Public/Products/Category', [
            'category' => $category,
            'products' => $products,
            'categories' => $categories,
            'settings' => [
                'siteName' => $settings->site_name,
                'tagline' => $settings->tagline,
                'logoPath' => $settings->logo_path,
                'showSiteNameInNav' => $settings->show_site_name_in_nav ?? true,
                'navLogoHeight' => $settings->nav_logo_height ?? 32,
                'primaryColor' => $settings->primary_color,
                'secondaryColor' => $settings->secondary_color,
                'accentColor' => $settings->accent_color,
                'backgroundColor' => $settings->background_color,
                'textColor' => $settings->text_color,
                'successColor' => $settings->success_color,
                'warningColor' => $settings->warning_color,
                'errorColor' => $settings->error_color,
                'infoColor' => $settings->info_color,
                'surfaceColor' => $settings->surface_color,
                'borderColor' => $settings->border_color,
                'mutedColor' => $settings->muted_color,
                'contactEmail' => $settings->contact_email,
                'contactPhone' => $settings->contact_phone,
                'contactAddress' => $settings->contact_address,
                'socialLinks' => $settings->social_links,
            ],
            'navigation' => $pageService->getNavigation(),
            'seo' => [
                'title' => $category->name . ' | Products | ' . $settings->site_name,
                'description' => $category->description ?? $settings->default_meta_description,
            ],
        ]);
    }

    /**
     * Display a single product.
     */
    public function show(
        Product $product,
        Request $request,
        ProductCategoryService $categoryService,
        PageService $pageService
    ): Response {
        // Only show active products to the public
        if (!$product->is_active) {
            // Allow preview for authorized users
            if (!$request->user() || !$request->user()->can('update', $product)) {
                abort(404);
            }
        }

        $product->load('category:id,name,slug');
        $categories = $categoryService->getCategoriesWithProducts();
        $settings = SiteSettings::current();

        $canEdit = $request->user()?->can('update', $product) ?? false;

        // Fetch related products from the same category
        $relatedProducts = collect([]);
        if ($product->category_id) {
            $relatedProducts = Product::active()
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->select(['id', 'name', 'slug', 'price', 'currency', 'image', 'is_featured'])
                ->limit(4)
                ->get();
        }

        return Inertia::render('Public/Products/Show', [
            'product' => $product,
            'categories' => $categories,
            'relatedProducts' => $relatedProducts,
            'settings' => [
                'siteName' => $settings->site_name,
                'tagline' => $settings->tagline,
                'logoPath' => $settings->logo_path,
                'showSiteNameInNav' => $settings->show_site_name_in_nav ?? true,
                'navLogoHeight' => $settings->nav_logo_height ?? 32,
                'primaryColor' => $settings->primary_color,
                'secondaryColor' => $settings->secondary_color,
                'accentColor' => $settings->accent_color,
                'backgroundColor' => $settings->background_color,
                'textColor' => $settings->text_color,
                'successColor' => $settings->success_color,
                'warningColor' => $settings->warning_color,
                'errorColor' => $settings->error_color,
                'infoColor' => $settings->info_color,
                'surfaceColor' => $settings->surface_color,
                'borderColor' => $settings->border_color,
                'mutedColor' => $settings->muted_color,
                'contactEmail' => $settings->contact_email,
                'contactPhone' => $settings->contact_phone,
                'contactAddress' => $settings->contact_address,
                'socialLinks' => $settings->social_links,
            ],
            'navigation' => $pageService->getNavigation(),
            'seo' => [
                'title' => $product->name . ' | Products | ' . $settings->site_name,
                'description' => $product->description ?? $settings->default_meta_description,
                'ogImage' => $product->image ?? $settings->og_default_image,
            ],
            'canEdit' => $canEdit,
            'editUrl' => $canEdit ? route('admin.products.edit', $product) : null,
        ]);
    }
}
