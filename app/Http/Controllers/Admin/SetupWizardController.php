<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setup\GenerateSiteRequest;
use App\Jobs\GenerateSiteJob;
use App\Services\SetupWizardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles the AI-powered site setup wizard.
 */
class SetupWizardController extends Controller
{
    /**
     * Display the setup wizard.
     */
    public function show(SetupWizardService $wizardService): Response
    {
        return Inertia::render('Admin/Setup/Wizard', [
            'config' => $wizardService->getWizardConfig(),
            'savedData' => $wizardService->getSavedWizardData(),
        ]);
    }

    /**
     * Generate the site from wizard data (async via job).
     */
    public function generate(GenerateSiteRequest $request): JsonResponse
    {
        $jobId = Str::uuid()->toString();

        // Initialize status in cache
        Cache::put(GenerateSiteJob::getCacheKey($jobId), [
            'status' => 'pending',
            'message' => 'Starting site generation...',
            'result' => null,
            'updated_at' => now()->toIso8601String(),
        ], now()->addHours(1));

        // Dispatch the job
        GenerateSiteJob::dispatch(
            $request->user()->id,
            $request->validatedWithDefaults(),
            $jobId
        );

        return response()->json([
            'job_id' => $jobId,
            'status' => 'pending',
            'message' => 'Site generation started',
        ]);
    }

    /**
     * Check the status of a site generation job.
     */
    public function status(string $jobId): JsonResponse
    {
        $status = Cache::get(GenerateSiteJob::getCacheKey($jobId));

        if (!$status) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Job not found or expired',
            ], 404);
        }

        return response()->json($status);
    }

    /**
     * Generate the site synchronously (fallback for non-queue environments).
     */
    public function generateSync(GenerateSiteRequest $request, SetupWizardService $wizardService): RedirectResponse
    {
        try {
            $result = $wizardService->generate(
                $request->user(),
                $request->validatedWithDefaults()
            );

            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Your site has been generated successfully! ' . $result['pages_created'] . ' pages were created.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to generate site: ' . $e->getMessage());
        }
    }
}
