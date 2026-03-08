<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\PageService;
use Illuminate\Http\RedirectResponse;

/**
 * Handles duplicating a page.
 */
class DuplicatePageController extends Controller
{
    /**
     * Duplicate the specified page.
     */
    public function __invoke(Page $page, PageService $service): RedirectResponse
    {
        $this->authorize('create', Page::class);

        $newPage = $service->duplicate(request()->user(), $page);

        return redirect()
            ->route('admin.pages.edit', $newPage)
            ->with('success', 'Page duplicated successfully.');
    }
}
