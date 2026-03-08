<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\SiteSettings;
use App\Services\PageService;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Renders public-facing CMS pages.
 */
class PublicPageController extends Controller
{
    public function __invoke(
        string $path,
        Request $request,
        PageService $pageService,
        SeoService $seoService
    ): Response {
        $normalizedPath = '/' . ltrim($path, '/');
        $normalizedPath = rtrim($normalizedPath, '/') ?: '/';

        // First try to find a published page
        $page = $pageService->findByPath($normalizedPath);
        $isPreview = false;

        // If not found and user is logged in, check for unpublished page (preview mode)
        if (!$page && $request->user()) {
            $page = Page::where('path', $normalizedPath)->first();

            if ($page && $request->user()->can('update', $page)) {
                $isPreview = true;
            } else {
                $page = null;
            }
        }

        if (!$page) {
            abort(404);
        }

        // For preview mode or pages with unpublished changes (when user can edit),
        // show draft content instead of published content
        $canEdit = $request->user()?->can('update', $page) ?? false;
        if ($canEdit && ($isPreview || $page->has_unpublished_changes)) {
            $isPreview = true;
            // Clone the page and replace content with draft content for preview
            $page = clone $page;
            $page->content = $page->draft_content ?? $page->content;
            $page->sidebar_content = $page->draft_sidebar_content ?? $page->sidebar_content;
        }

        $settings = SiteSettings::current();

        return Inertia::render('Public/Page', [
            'page' => $page,
            'seo' => $seoService->buildForPage($page),
            'settings' => [
                'siteName' => $settings->site_name,
                'tagline' => $settings->tagline,
                'logoPath' => $settings->logo_path,
                'showSiteNameInNav' => $settings->show_site_name_in_nav ?? true,
                'navLogoHeight' => $settings->nav_logo_height ?? 32,
                'primaryColor' => $settings->primary_color,
                'secondaryColor' => $settings->secondary_color,
                'accentColor' => $settings->accent_color,
                'backgroundColor' => $settings->background_color,
                'textColor' => $settings->text_color,
                'contactEmail' => $settings->contact_email,
                'contactPhone' => $settings->contact_phone,
                'contactAddress' => $settings->contact_address,
                'socialLinks' => $settings->social_links,
            ],
            'navigation' => $pageService->getNavigation(),
            'canEdit' => $canEdit,
            'editUrl' => $canEdit ? route('admin.pages.edit', $page) : null,
            'isPreview' => $isPreview,
        ]);
    }
}
