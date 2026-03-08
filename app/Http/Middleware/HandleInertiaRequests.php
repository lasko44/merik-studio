<?php

namespace App\Http\Middleware;

use App\Models\SiteSettings;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
            ],
            'siteSettings' => fn () => $this->getSiteSettings(),
            'seo' => fn () => $this->getDefaultSeo(),
        ];
    }

    /**
     * Get cached site settings for frontend.
     */
    private function getSiteSettings(): array
    {
        $settings = SiteSettings::current();

        return [
            'siteName' => $settings->site_name,
            'tagline' => $settings->tagline,
            'logoPath' => $settings->logo_path,
            'faviconPath' => $settings->favicon_path,
            'primaryColor' => $settings->primary_color,
            'secondaryColor' => $settings->secondary_color,
            'accentColor' => $settings->accent_color,
            'backgroundColor' => $settings->background_color,
            'textColor' => $settings->text_color,
            'successColor' => $settings->success_color,
            'warningColor' => $settings->warning_color,
            'errorColor' => $settings->error_color,
            'infoColor' => $settings->info_color,
            'surfaceColor' => $settings->surface_color,
            'borderColor' => $settings->border_color,
            'mutedColor' => $settings->muted_color,
        ];
    }

    /**
     * Get default SEO data.
     */
    private function getDefaultSeo(): array
    {
        $seoService = app(SeoService::class);

        return $seoService->buildDefaults();
    }
}
