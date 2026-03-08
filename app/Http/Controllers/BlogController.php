<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\SiteSettings;
use App\Services\BlogCategoryService;
use App\Services\BlogPostService;
use App\Services\PageService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Renders public-facing blog pages.
 */
class BlogController extends Controller
{
    /**
     * Display the blog listing page.
     */
    public function index(
        Request $request,
        BlogPostService $postService,
        BlogCategoryService $categoryService,
        PageService $pageService
    ): Response {
        $search = $request->input('search');
        $categorySlug = $request->input('category');

        $posts = $postService->getPublishedPosts(10, $search, $categorySlug);
        $categories = $categoryService->getCategoriesWithPosts();
        $settings = SiteSettings::current();

        return Inertia::render('Public/Blog/Index', [
            'posts' => $posts,
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
            'blogSettings' => [
                'enableSearch' => $settings->blog_enable_search ?? true,
                'enableCategoryFilter' => $settings->blog_enable_category_filter ?? true,
            ],
            'filters' => [
                'search' => $search,
                'category' => $categorySlug,
            ],
            'navigation' => $pageService->getNavigation(),
            'seo' => [
                'title' => 'Blog | ' . $settings->site_name,
                'description' => $settings->default_meta_description,
            ],
        ]);
    }

    /**
     * Display posts by category.
     */
    public function category(
        BlogCategory $category,
        BlogPostService $postService,
        BlogCategoryService $categoryService,
        PageService $pageService
    ): Response {
        $posts = $postService->getPostsByCategory($category->id, 10);
        $categories = $categoryService->getCategoriesWithPosts();
        $settings = SiteSettings::current();

        return Inertia::render('Public/Blog/Category', [
            'category' => $category,
            'posts' => $posts,
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
                'contactEmail' => $settings->contact_email,
                'contactPhone' => $settings->contact_phone,
                'contactAddress' => $settings->contact_address,
                'socialLinks' => $settings->social_links,
            ],
            'navigation' => $pageService->getNavigation(),
            'seo' => [
                'title' => $category->name . ' | Blog | ' . $settings->site_name,
                'description' => $category->description ?? $settings->default_meta_description,
            ],
        ]);
    }

    /**
     * Display a single blog post.
     */
    public function show(
        BlogPost $post,
        Request $request,
        BlogCategoryService $categoryService,
        PageService $pageService
    ): Response {
        // Check if post is published or if user can preview
        $isPreview = false;
        $canEdit = false;

        if (!$post->is_published || !$post->published_at) {
            // Only allow preview for authorized users
            if ($request->user() && $request->user()->can('update', $post)) {
                $isPreview = true;
                $canEdit = true;
            } else {
                abort(404);
            }
        } else {
            $canEdit = $request->user()?->can('update', $post) ?? false;

            // Check for preview of unpublished changes
            if ($canEdit && $post->has_unpublished_changes) {
                $isPreview = true;
                $post = clone $post;
                $post->content = $post->draft_content ?? $post->content;
            }
        }

        $post->load(['category:id,name,slug', 'author:id,name']);
        $categories = $categoryService->getCategoriesWithPosts();
        $settings = SiteSettings::current();

        return Inertia::render('Public/Blog/Show', [
            'post' => $post,
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
                'contactEmail' => $settings->contact_email,
                'contactPhone' => $settings->contact_phone,
                'contactAddress' => $settings->contact_address,
                'socialLinks' => $settings->social_links,
            ],
            'navigation' => $pageService->getNavigation(),
            'seo' => [
                'title' => $post->title . ' | Blog | ' . $settings->site_name,
                'description' => $post->meta_description ?? $post->excerpt ?? $settings->default_meta_description,
                'ogTitle' => $post->og_title ?? $post->title,
                'ogDescription' => $post->og_description ?? $post->meta_description ?? $post->excerpt,
                'ogImage' => $post->og_image ?? $post->featured_image ?? $settings->og_default_image,
                'noIndex' => $post->no_index,
            ],
            'canEdit' => $canEdit,
            'editUrl' => $canEdit ? route('admin.blog.posts.edit', $post) : null,
            'isPreview' => $isPreview,
        ]);
    }
}
