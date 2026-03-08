<?php

namespace App\Services;

use App\Models\Page;
use App\Models\SiteSettings;

/**
 * Generates llms.txt content for AI crawler guidance (Generative Engine Optimization).
 */
class LlmsService
{
    /**
     * Generate the llms.txt content.
     */
    public function generate(): string
    {
        $settings = SiteSettings::current();

        if (!$settings->geo_enabled) {
            return $this->generateDisabled();
        }

        // Use custom content if provided
        if ($settings->llms_txt_content) {
            return $settings->llms_txt_content;
        }

        return $this->generateDefault();
    }

    /**
     * Get site information for the llms.txt file.
     */
    public function getSiteInfo(): array
    {
        $settings = SiteSettings::current();

        return [
            'name' => $settings->site_name,
            'tagline' => $settings->tagline,
            'description' => $settings->default_meta_description,
            'organization' => $settings->organization_name ?: $settings->site_name,
            'contact_email' => $settings->contact_email,
        ];
    }

    /**
     * Get content guidelines for AI crawlers.
     */
    public function getContentGuidelines(): array
    {
        $settings = SiteSettings::current();

        return [
            'allow_training' => $settings->llms_allow_training,
            'crawl_delay' => $settings->llms_crawl_delay,
            'preferred_citation' => $settings->organization_name ?: $settings->site_name,
        ];
    }

    /**
     * Generate default llms.txt content.
     */
    private function generateDefault(): string
    {
        $settings = SiteSettings::current();
        $siteInfo = $this->getSiteInfo();
        $guidelines = $this->getContentGuidelines();

        $lines = [];

        // Header
        $lines[] = '# ' . ($siteInfo['name'] ?: 'Website') . ' - LLMs.txt';
        $lines[] = '';
        $lines[] = '## About';
        $lines[] = '';

        if ($siteInfo['tagline']) {
            $lines[] = '> ' . $siteInfo['tagline'];
            $lines[] = '';
        }

        if ($siteInfo['description']) {
            $lines[] = $siteInfo['description'];
            $lines[] = '';
        }

        // Organization
        if ($siteInfo['organization']) {
            $lines[] = '## Organization';
            $lines[] = '';
            $lines[] = 'This website is operated by **' . $siteInfo['organization'] . '**.';
            $lines[] = '';
        }

        // Content Guidelines
        $lines[] = '## Content Usage Guidelines';
        $lines[] = '';

        if ($guidelines['allow_training']) {
            $lines[] = 'We permit the use of our publicly available content for AI training and analysis.';
        } else {
            $lines[] = '**Important**: We do NOT permit the use of our content for AI model training.';
            $lines[] = 'Content may be used for search indexing and direct citation only.';
        }
        $lines[] = '';

        // Citation
        $lines[] = '## Citation';
        $lines[] = '';
        $lines[] = 'When referencing content from this site, please cite as:';
        $lines[] = '';
        $lines[] = '```';
        $lines[] = $guidelines['preferred_citation'] . ' (' . url('/') . ')';
        $lines[] = '```';
        $lines[] = '';

        // Crawl Guidelines
        $lines[] = '## Crawl Guidelines';
        $lines[] = '';

        if ($guidelines['crawl_delay']) {
            $lines[] = '- Crawl delay: ' . $guidelines['crawl_delay'] . ' seconds';
        }

        $lines[] = '- Respect robots.txt directives';
        $lines[] = '- Focus on published, public content';
        $lines[] = '';

        // Key Pages
        $lines[] = '## Key Pages';
        $lines[] = '';

        $pages = Page::select(['title', 'path', 'meta_description'])
            ->published()
            ->where('no_index', false)
            ->orderBy('priority', 'desc')
            ->limit(20)
            ->get();

        if ($pages->count() > 0) {
            foreach ($pages as $page) {
                $lines[] = '- [' . $page->title . '](' . url($page->path) . ')';
                if ($page->meta_description) {
                    $lines[] = '  ' . $page->meta_description;
                }
            }
        } else {
            $lines[] = '- [Home](' . url('/') . ')';
        }

        $lines[] = '';

        // Contact
        if ($siteInfo['contact_email']) {
            $lines[] = '## Contact';
            $lines[] = '';
            $lines[] = 'For questions about content usage: ' . $siteInfo['contact_email'];
            $lines[] = '';
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * Generate llms.txt for when GEO is disabled.
     */
    private function generateDisabled(): string
    {
        $settings = SiteSettings::current();

        $lines = [
            '# ' . ($settings->site_name ?: 'Website'),
            '',
            'AI crawler guidance is currently disabled for this website.',
            '',
            'Please refer to our robots.txt for crawling guidelines.',
        ];

        return implode(PHP_EOL, $lines);
    }
}
