<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\StoreBlogCategoryRequest;
use App\Http\Requests\Blog\UpdateBlogCategoryRequest;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Services\BlogCategoryService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles CRUD operations for blog categories in the admin panel.
 */
class BlogCategoryController extends Controller
{
    /**
     * Display a listing of all blog categories.
     */
    public function index(BlogCategoryService $service): Response
    {
        $this->authorize('viewAny', BlogPost::class);

        $categories = $service->getAllForAdmin();

        return Inertia::render('Admin/Blog/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new blog category.
     */
    public function create(): Response
    {
        $this->authorize('create', BlogPost::class);

        return Inertia::render('Admin/Blog/Categories/Create');
    }

    /**
     * Store a newly created blog category.
     */
    public function store(StoreBlogCategoryRequest $request, BlogCategoryService $service): RedirectResponse
    {
        $category = $service->create($request->validated());

        return redirect()
            ->route('admin.blog.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified blog category for editing.
     */
    public function show(BlogCategory $category): RedirectResponse
    {
        return redirect()->route('admin.blog.categories.edit', $category);
    }

    /**
     * Show the form for editing the specified blog category.
     */
    public function edit(BlogCategory $category): Response
    {
        $this->authorize('update', BlogPost::class);

        return Inertia::render('Admin/Blog/Categories/Edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified blog category.
     */
    public function update(UpdateBlogCategoryRequest $request, BlogCategory $category, BlogCategoryService $service): RedirectResponse
    {
        $service->update($category, $request->validated());

        return redirect()
            ->route('admin.blog.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified blog category.
     */
    public function destroy(BlogCategory $category, BlogCategoryService $service): RedirectResponse
    {
        $this->authorize('delete', BlogPost::class);

        $service->delete($category);

        return redirect()
            ->route('admin.blog.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
