<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pages\StorePageRequest;
use App\Http\Requests\Pages\UpdatePageRequest;
use App\Models\Page;
use App\Models\PageComponent;
use App\Services\PageService;
use App\Services\TemplateService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles CRUD operations for CMS pages in the admin panel.
 */
class PageController extends Controller
{
    /**
     * Display a listing of all pages.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Page::class);

        $pages = Page::select([
            'id',
            'title',
            'path',
            'is_published',
            'is_admin_page',
            'sort_order',
            'created_by',
            'updated_by',
            'updated_at',
        ])
            ->with(['creator:id,name', 'updater:id,name'])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return Inertia::render('Admin/Pages/Index', [
            'pages' => $pages,
            'pageLimit' => Page::getPageLimit(),
            'remainingPages' => Page::remainingPublicPages(),
        ]);
    }

    /**
     * Show the form for creating a new page.
     */
    public function create(TemplateService $templateService, PageService $pageService): Response
    {
        $this->authorize('create', Page::class);

        return Inertia::render('Admin/Pages/Create', [
            'components' => PageComponent::groupedByCategory(),
            'templateConfig' => $templateService->getForFrontend(),
            'availablePages' => $pageService->getAvailablePagesForLinks(),
        ]);
    }

    /**
     * Store a newly created page.
     */
    public function store(StorePageRequest $request, PageService $service): RedirectResponse
    {
        $page = $service->create($request->user(), $request->validated());

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('success', 'Page created successfully.');
    }

    /**
     * Display the specified page for editing.
     */
    public function show(Page $page): RedirectResponse
    {
        return redirect()->route('admin.pages.edit', $page);
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(Page $page, TemplateService $templateService, PageService $pageService): Response
    {
        $this->authorize('update', $page);

        $page->load(['creator:id,name', 'updater:id,name']);

        return Inertia::render('Admin/Pages/Edit', [
            'page' => $page,
            'components' => PageComponent::groupedByCategory(),
            'templateConfig' => $templateService->getForFrontend(),
            'availablePages' => $pageService->getAvailablePagesForLinks(),
        ]);
    }

    /**
     * Update the specified page (save draft only).
     */
    public function update(UpdatePageRequest $request, Page $page, PageService $service): RedirectResponse
    {
        $publish = $request->boolean('publish', false);
        $service->update($request->user(), $page, $request->validated(), $publish);

        $message = $publish ? 'Page saved and published.' : 'Draft saved.';

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('success', $message);
    }

    /**
     * Remove the specified page.
     */
    public function destroy(Page $page, PageService $service): RedirectResponse
    {
        $this->authorize('delete', $page);

        $service->delete($page);

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }
}
