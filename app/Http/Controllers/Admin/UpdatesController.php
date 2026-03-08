<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UpdateService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles CMS update information and version management.
 */
class UpdatesController extends Controller
{
    /**
     * Display the updates page.
     */
    public function index(UpdateService $service): Response
    {
        return Inertia::render('Admin/Updates/Index', [
            'versionInfo' => $service->getVersionInfo(),
            'githubRepo' => config('version.github_repo'),
        ]);
    }

    /**
     * Force a fresh update check.
     */
    public function check(UpdateService $service): JsonResponse
    {
        $result = $service->forceCheck();

        return response()->json([
            'versionInfo' => $service->getVersionInfo(),
        ]);
    }
}
