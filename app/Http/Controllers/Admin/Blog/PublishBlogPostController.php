<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Services\BlogPostService;
use Illuminate\Http\RedirectResponse;

/**
 * Handles publishing a blog post.
 */
class PublishBlogPostController extends Controller
{
    /**
     * Publish the specified blog post.
     */
    public function __invoke(BlogPost $post, BlogPostService $service): RedirectResponse
    {
        $this->authorize('update', $post);

        $service->publish(request()->user(), $post);

        return back()->with('success', 'Post published successfully.');
    }
}
