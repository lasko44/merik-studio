<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\PageService;
use Illuminate\Http\RedirectResponse;

/**
 * Handles publishing a page.
 */
class PublishPageController extends Controller
{
    /**
     * Publish the specified page.
     */
    public function __invoke(Page $page, PageService $service): RedirectResponse
    {
        $this->authorize('update', $page);

        $service->publish(request()->user(), $page);

        return back()->with('success', 'Page published successfully.');
    }
}
