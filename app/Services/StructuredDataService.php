<?php

namespace App\Services;

use App\Models\Page;
use App\Models\SiteSettings;
use Illuminate\Support\Arr;

/**
 * Generates JSON-LD structured data for SEO and AI search optimization.
 */
class StructuredDataService
{
    /**
     * Build all structured data schemas for a page.
     */
    public function buildForPage(Page $page): array
    {
        $schemas = [
            $this->buildWebSiteSchema(),
            $this->buildOrganizationSchema(),
        ];

        $schemas[] = $this->buildPageSchema($page);

        if ($page->faqs && count($page->faqs) > 0) {
            $schemas[] = $this->buildFaqSchema($page->faqs);
        }

        $breadcrumbs = $this->buildBreadcrumbSchema($page);
        if ($breadcrumbs) {
            $schemas[] = $breadcrumbs;
        }

        if ($page->speakable_selectors && count($page->speakable_selectors) > 0) {
            $schemas[] = $this->buildSpeakableSchema($page);
        }

        return $schemas;
    }

    /**
     * Build WebPage or Article schema based on page type.
     */
    public function buildPageSchema(Page $page): array
    {
        $settings = SiteSettings::current();
        $schemaType = $page->schema_type ?: 'WebPage';

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $schemaType,
            '@id' => url($page->path) . '#' . strtolower($schemaType),
            'url' => url($page->path),
            'name' => $page->title,
            'description' => $page->meta_description ?: $settings->default_meta_description,
            'isPartOf' => [
                '@id' => url('/') . '#website',
            ],
            'datePublished' => $page->created_at->toIso8601String(),
            'dateModified' => $page->updated_at->toIso8601String(),
        ];

        if ($schemaType === 'Article' || $schemaType === 'BlogPosting') {
            $schema['author'] = [
                '@id' => url('/') . '#organization',
            ];
            $schema['publisher'] = [
                '@id' => url('/') . '#organization',
            ];
        }

        if ($page->og_image) {
            $schema['image'] = url($page->og_image);
        } elseif ($settings->og_default_image) {
            $schema['image'] = url($settings->og_default_image);
        }

        return $schema;
    }

    /**
     * Build WebPage schema.
     */
    public function buildWebPageSchema(Page $page): array
    {
        return $this->buildPageSchema($page);
    }

    /**
     * Build Article schema.
     */
    public function buildArticleSchema(Page $page): array
    {
        $schema = $this->buildPageSchema($page);
        $schema['@type'] = 'Article';

        return $schema;
    }

    /**
     * Build Organization schema from site settings.
     */
    public function buildOrganizationSchema(): array
    {
        $settings = SiteSettings::current();

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $settings->organization_type ?: 'Organization',
            '@id' => url('/') . '#organization',
            'name' => $settings->organization_name ?: $settings->site_name,
            'url' => url('/'),
        ];

        if ($settings->organization_logo) {
            $schema['logo'] = [
                '@type' => 'ImageObject',
                '@id' => url('/') . '#logo',
                'url' => url($settings->organization_logo),
                'contentUrl' => url($settings->organization_logo),
            ];
            $schema['image'] = ['@id' => url('/') . '#logo'];
        } elseif ($settings->logo_path) {
            $schema['logo'] = [
                '@type' => 'ImageObject',
                '@id' => url('/') . '#logo',
                'url' => url($settings->logo_path),
                'contentUrl' => url($settings->logo_path),
            ];
            $schema['image'] = ['@id' => url('/') . '#logo'];
        }

        if ($settings->contact_email) {
            $schema['email'] = $settings->contact_email;
        }

        if ($settings->contact_phone) {
            $schema['telephone'] = $settings->contact_phone;
        }

        if ($settings->contact_address) {
            $schema['address'] = $settings->contact_address;
        }

        if ($settings->same_as && is_array($settings->same_as)) {
            $schema['sameAs'] = array_values($settings->same_as);
        } elseif ($settings->social_links && is_array($settings->social_links)) {
            $urls = array_filter(array_map(fn ($link) => Arr::get($link, 'url'), $settings->social_links));
            if (count($urls) > 0) {
                $schema['sameAs'] = array_values($urls);
            }
        }

        return $schema;
    }

    /**
     * Build WebSite schema.
     */
    public function buildWebSiteSchema(): array
    {
        $settings = SiteSettings::current();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => url('/') . '#website',
            'url' => url('/'),
            'name' => $settings->site_name,
            'description' => $settings->tagline ?: $settings->default_meta_description,
            'publisher' => [
                '@id' => url('/') . '#organization',
            ],
        ];
    }

    /**
     * Build FAQ schema from array of FAQ items.
     */
    public function buildFaqSchema(array $faqs): array
    {
        $mainEntity = [];

        foreach ($faqs as $faq) {
            $question = Arr::get($faq, 'question');
            $answer = Arr::get($faq, 'answer');

            if ($question && $answer) {
                $mainEntity[] = [
                    '@type' => 'Question',
                    'name' => $question,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $answer,
                    ],
                ];
            }
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity,
        ];
    }

    /**
     * Build BreadcrumbList schema for a page.
     */
    public function buildBreadcrumbSchema(Page $page): ?array
    {
        $settings = SiteSettings::current();

        $items = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => url('/'),
            ],
        ];

        if ($page->path !== '/') {
            $items[] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $page->title,
                'item' => url($page->path),
            ];
        } else {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /**
     * Build Speakable schema for voice assistant optimization.
     */
    public function buildSpeakableSchema(Page $page): array
    {
        $cssSelectors = $page->speakable_selectors ?: ['h1', '.speakable'];

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'speakable' => [
                '@type' => 'SpeakableSpecification',
                'cssSelector' => $cssSelectors,
            ],
        ];
    }
}
