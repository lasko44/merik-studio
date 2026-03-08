<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\UpdateService;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles the admin dashboard display.
 */
class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function __invoke(UpdateService $updateService): Response
    {
        $totalPages = Page::count();
        $publishedPages = Page::where('is_published', true)->count();
        $draftPages = Page::where('is_published', false)->count();

        $recentPages = Page::select(['id', 'title', 'path', 'is_published', 'updated_at'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        $updateInfo = $this->getUpdateInfoForUser($updateService);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalPages' => $totalPages,
                'publishedPages' => $publishedPages,
                'draftPages' => $draftPages,
            ],
            'recentPages' => $recentPages,
            'pageLimit' => Page::getPageLimit(),
            'remainingPages' => Page::remainingPublicPages(),
            'updateInfo' => $updateInfo,
        ]);
    }

    /**
     * Get update info if user should see it.
     */
    protected function getUpdateInfoForUser(UpdateService $updateService): ?array
    {
        $user = auth()->user();
        $notifyRoles = config('version.notify_roles', ['Super Admin', 'Admin']);

        if (!$user || !$user->hasAnyRole($notifyRoles)) {
            return null;
        }

        $info = $updateService->checkForUpdates();

        return [
            'available' => Arr::get($info, 'available', false),
            'current_version' => Arr::get($info, 'current_version'),
            'latest_version' => Arr::get($info, 'latest_version'),
            'release_name' => Arr::get($info, 'release_name'),
        ];
    }
}
