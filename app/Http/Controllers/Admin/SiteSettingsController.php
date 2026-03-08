<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateSiteSettingsRequest;
use App\Models\SiteSettings;
use App\Services\SiteSettingsService;
use App\Services\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles site settings management in the admin panel.
 */
class SiteSettingsController extends Controller
{
    /**
     * Show the form for editing site settings.
     */
    public function edit(SiteSettingsService $service): Response
    {
        $this->authorize('settings.view');

        $settings = SiteSettings::current();

        return Inertia::render('Admin/Settings/Edit', [
            'settings' => $settings,
            'themes' => SiteSettings::THEMES,
            'fonts' => SiteSettings::FONTS,
            'organizationTypes' => $service->getOrganizationTypes(),
            'stripeEnabled' => config('cms.stripe_enabled', false),
            'stripeSettings' => [
                'publishable_key' => $settings->stripe_publishable_key,
                'secret_key_set' => !empty($settings->stripe_secret_key),
                'webhook_secret_set' => !empty($settings->stripe_webhook_secret),
                'test_mode' => $settings->stripe_test_mode,
            ],
        ]);
    }

    /**
     * Update the site settings.
     */
    public function update(UpdateSiteSettingsRequest $request, SiteSettingsService $service): RedirectResponse
    {
        $service->update($request->validated());

        return back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Test Stripe API connection.
     */
    public function testStripeConnection(StripeService $stripeService): JsonResponse
    {
        $this->authorize('settings.update');

        $result = $stripeService->testConnection();

        return response()->json($result);
    }
}
