<?php

namespace App\Services;

use App\Models\Page;
use App\Models\SiteSettings;

/**
 * Generates XML sitemap for search engine crawlers.
 */
class SitemapService
{
    /**
     * Generate the complete sitemap XML.
     */
    public function generate(): string
    {
        $settings = SiteSettings::current();

        if (!$settings->sitemap_enabled) {
            return $this->generateEmptySitemap();
        }

        $urls = $this->getUrls();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $url) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8') . '</loc>' . PHP_EOL;

            if (isset($url['lastmod'])) {
                $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . PHP_EOL;
            }

            if (isset($url['changefreq'])) {
                $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . PHP_EOL;
            }

            if (isset($url['priority'])) {
                $xml .= '    <priority>' . $url['priority'] . '</priority>' . PHP_EOL;
            }

            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Get all URLs for the sitemap.
     */
    public function getUrls(): array
    {
        $urls = [];

        // Add homepage
        $urls[] = [
            'loc' => url('/'),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        // Add published pages
        $pages = Page::select(['id', 'path', 'updated_at', 'priority', 'change_frequency', 'no_index'])
            ->published()
            ->where('no_index', false)
            ->orderBy('priority', 'desc')
            ->cursor();

        foreach ($pages as $page) {
            $urls[] = $this->buildUrlEntry($page);
        }

        return $urls;
    }

    /**
     * Build a sitemap URL entry for a page.
     */
    public function buildUrlEntry(Page $page): array
    {
        return [
            'loc' => url($page->path),
            'lastmod' => $page->updated_at->toW3cString(),
            'changefreq' => $page->change_frequency ?: 'weekly',
            'priority' => number_format((float) ($page->priority ?: 0.5), 1),
        ];
    }

    /**
     * Generate an empty sitemap when disabled.
     */
    private function generateEmptySitemap(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        $xml .= '</urlset>';

        return $xml;
    }
}
