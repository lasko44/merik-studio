<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Services\BlogPostService;
use Illuminate\Http\RedirectResponse;

/**
 * Handles unpublishing a blog post.
 */
class UnpublishBlogPostController extends Controller
{
    /**
     * Unpublish the specified blog post.
     */
    public function __invoke(BlogPost $post, BlogPostService $service): RedirectResponse
    {
        $this->authorize('update', $post);

        $service->unpublish(request()->user(), $post);

        return back()->with('success', 'Post unpublished successfully.');
    }
}
