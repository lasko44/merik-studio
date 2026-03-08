<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Navigation\UpdateNavigationRequest;
use App\Models\Page;
use App\Services\NavigationService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Manages the site navigation structure and page hierarchy.
 */
class NavigationController extends Controller
{
    /**
     * Display the navigation editor.
     */
    public function edit(NavigationService $service): Response
    {
        $this->authorize('viewAny', Page::class);

        return Inertia::render('Admin/Navigation/Edit', $service->getEditorData());
    }

    /**
     * Update the navigation structure.
     */
    public function update(UpdateNavigationRequest $request, NavigationService $service): RedirectResponse
    {
        $service->updateStructure($request->validated());

        return redirect()
            ->route('admin.navigation.edit')
            ->with('success', 'Navigation updated successfully.');
    }
}
