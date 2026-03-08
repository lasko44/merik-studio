<?php

namespace App\Services;

use App\Models\Page;
use App\Models\SiteSettings;

/**
 * Builds SEO metadata including meta tags, Open Graph, and Twitter Cards.
 */
class SeoService
{
    public function __construct(
        protected StructuredDataService $structuredDataService,
    ) {}

    /**
     * Build complete SEO data for a page.
     */
    public function buildForPage(Page $page): array
    {
        return [
            'meta' => $this->getMetaTags($page),
            'openGraph' => $this->getOpenGraphTags($page),
            'twitter' => $this->getTwitterCardTags($page),
            'canonical' => $this->getCanonicalUrl($page),
            'robots' => $this->getRobotsMeta($page),
            'jsonLd' => $this->structuredDataService->buildForPage($page),
        ];
    }

    /**
     * Build default SEO data when no page context is available.
     */
    public function buildDefaults(): array
    {
        $settings = SiteSettings::current();

        return [
            'meta' => [
                'title' => $settings->site_name,
                'description' => $settings->default_meta_description,
                'keywords' => $settings->default_meta_keywords,
            ],
            'openGraph' => [
                'type' => 'website',
                'title' => $settings->site_name,
                'description' => $settings->default_meta_description,
                'image' => $settings->og_default_image ? url($settings->og_default_image) : null,
                'url' => url('/'),
                'siteName' => $settings->site_name,
            ],
            'twitter' => [
                'card' => $settings->twitter_card_type ?: 'summary_large_image',
                'site' => $settings->twitter_handle,
                'title' => $settings->site_name,
                'description' => $settings->default_meta_description,
                'image' => $settings->og_default_image ? url($settings->og_default_image) : null,
            ],
            'canonical' => url('/'),
            'robots' => 'index, follow',
            'jsonLd' => [
                $this->structuredDataService->buildWebSiteSchema(),
                $this->structuredDataService->buildOrganizationSchema(),
            ],
        ];
    }

    /**
     * Get meta tags for a page.
     */
    public function getMetaTags(Page $page): array
    {
        $settings = SiteSettings::current();

        return [
            'title' => $page->title,
            'titleTemplate' => '%s | ' . $settings->site_name,
            'description' => $page->meta_description ?: $settings->default_meta_description,
            'keywords' => $page->meta_keywords ?: $settings->default_meta_keywords,
        ];
    }

    /**
     * Get Open Graph tags for a page.
     */
    public function getOpenGraphTags(Page $page): array
    {
        $settings = SiteSettings::current();

        $image = $page->og_image ?: $settings->og_default_image;

        return [
            'type' => $this->getOpenGraphType($page->schema_type),
            'title' => $page->og_title ?: $page->title,
            'description' => $page->og_description ?: $page->meta_description ?: $settings->default_meta_description,
            'image' => $image ? url($image) : null,
            'url' => $page->canonical_url ?: url($page->path),
            'siteName' => $settings->site_name,
        ];
    }

    /**
     * Get Twitter Card tags for a page.
     */
    public function getTwitterCardTags(Page $page): array
    {
        $settings = SiteSettings::current();

        $image = $page->og_image ?: $settings->og_default_image;

        return [
            'card' => $page->twitter_card_type ?: $settings->twitter_card_type ?: 'summary_large_image',
            'site' => $settings->twitter_handle,
            'title' => $page->og_title ?: $page->title,
            'description' => $page->og_description ?: $page->meta_description ?: $settings->default_meta_description,
            'image' => $image ? url($image) : null,
        ];
    }

    /**
     * Get canonical URL for a page.
     */
    public function getCanonicalUrl(Page $page): string
    {
        return $page->canonical_url ?: url($page->path);
    }

    /**
     * Get robots meta directive for a page.
     */
    public function getRobotsMeta(Page $page): string
    {
        $directives = [];

        $directives[] = $page->no_index ? 'noindex' : 'index';
        $directives[] = $page->no_follow ? 'nofollow' : 'follow';

        return implode(', ', $directives);
    }

    /**
     * Map schema type to Open Graph type.
     */
    private function getOpenGraphType(string $schemaType): string
    {
        return match ($schemaType) {
            'Article', 'BlogPosting', 'NewsArticle' => 'article',
            'Product' => 'product',
            'LocalBusiness', 'Organization' => 'business.business',
            default => 'website',
        };
    }
}
