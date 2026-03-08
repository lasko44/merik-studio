<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\StoreBlogPostRequest;
use App\Http\Requests\Blog\UpdateBlogPostRequest;
use App\Models\BlogPost;
use App\Models\PageComponent;
use App\Models\SiteSettings;
use App\Services\BlogCategoryService;
use App\Services\BlogPostService;
use App\Services\PageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles CRUD operations for blog posts in the admin panel.
 */
class BlogPostController extends Controller
{
    /**
     * Display a listing of all blog posts.
     */
    public function index(BlogPostService $service): Response
    {
        $this->authorize('viewAny', BlogPost::class);

        $posts = $service->getAllForAdmin();
        $settings = SiteSettings::first();

        return Inertia::render('Admin/Blog/Index', [
            'posts' => $posts,
            'blogSettings' => [
                'showInNav' => $settings->blog_show_in_nav ?? false,
                'navLabel' => $settings->blog_nav_label ?? 'Blog',
                'enableSearch' => $settings->blog_enable_search ?? true,
                'enableCategoryFilter' => $settings->blog_enable_category_filter ?? true,
            ],
        ]);
    }

    /**
     * Show the form for creating a new blog post.
     */
    public function create(BlogCategoryService $categoryService, PageService $pageService): Response
    {
        $this->authorize('create', BlogPost::class);

        return Inertia::render('Admin/Blog/Create', [
            'categories' => $categoryService->getAllForSelect(),
            'components' => PageComponent::groupedByCategory(),
            'availablePages' => $pageService->getAvailablePagesForLinks(),
        ]);
    }

    /**
     * Store a newly created blog post.
     */
    public function store(StoreBlogPostRequest $request, BlogPostService $service): RedirectResponse
    {
        $post = $service->create($request->user(), $request->validated());

        return redirect()
            ->route('admin.blog.posts.edit', $post)
            ->with('success', 'Blog post created successfully.');
    }

    /**
     * Display the specified blog post for editing.
     */
    public function show(BlogPost $post): RedirectResponse
    {
        return redirect()->route('admin.blog.posts.edit', $post);
    }

    /**
     * Show the form for editing the specified blog post.
     */
    public function edit(BlogPost $post, BlogCategoryService $categoryService, PageService $pageService): Response
    {
        $this->authorize('update', $post);

        $post->load(['category:id,name,slug', 'author:id,name', 'creator:id,name', 'updater:id,name']);

        return Inertia::render('Admin/Blog/Edit', [
            'post' => $post,
            'categories' => $categoryService->getAllForSelect(),
            'components' => PageComponent::groupedByCategory(),
            'availablePages' => $pageService->getAvailablePagesForLinks(),
        ]);
    }

    /**
     * Update the specified blog post (save draft only).
     */
    public function update(UpdateBlogPostRequest $request, BlogPost $post, BlogPostService $service): RedirectResponse
    {
        $publish = $request->boolean('publish', false);
        $service->update($request->user(), $post, $request->validated(), $publish);

        $message = $publish ? 'Post saved and published.' : 'Draft saved.';

        return redirect()
            ->route('admin.blog.posts.edit', $post)
            ->with('success', $message);
    }

    /**
     * Remove the specified blog post.
     */
    public function destroy(BlogPost $post, BlogPostService $service): RedirectResponse
    {
        $this->authorize('delete', $post);

        $service->delete($post);

        return redirect()
            ->route('admin.blog.posts.index')
            ->with('success', 'Blog post deleted successfully.');
    }

    /**
     * Update blog settings.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', BlogPost::class);

        $validated = $request->validate([
            'showInNav' => 'boolean',
            'navLabel' => 'required|string|max:50',
            'enableSearch' => 'boolean',
            'enableCategoryFilter' => 'boolean',
        ]);

        $settings = SiteSettings::first();
        $settings->update([
            'blog_show_in_nav' => $validated['showInNav'] ?? false,
            'blog_nav_label' => $validated['navLabel'],
            'blog_enable_search' => $validated['enableSearch'] ?? true,
            'blog_enable_category_filter' => $validated['enableCategoryFilter'] ?? true,
        ]);

        return redirect()
            ->route('admin.blog.posts.index')
            ->with('success', 'Blog settings updated successfully.');
    }
}
