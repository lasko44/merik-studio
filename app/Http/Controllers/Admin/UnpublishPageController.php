<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\PageService;
use Illuminate\Http\RedirectResponse;

/**
 * Handles unpublishing a page.
 */
class UnpublishPageController extends Controller
{
    /**
     * Unpublish the specified page.
     */
    public function __invoke(Page $page, PageService $service): RedirectResponse
    {
        $this->authorize('update', $page);

        $service->unpublish(request()->user(), $page);

        return back()->with('success', 'Page unpublished successfully.');
    }
}
