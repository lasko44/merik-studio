<?php

namespace App\Services;

use App\Models\SiteSettings;

/**
 * Generates robots.txt content for search engine crawlers.
 */
class RobotsService
{
    /**
     * Generate the robots.txt content.
     */
    public function generate(): string
    {
        $settings = SiteSettings::current();

        // Use custom content if provided
        if ($settings->robots_txt_content) {
            return $this->appendSitemap($settings->robots_txt_content);
        }

        // Generate default robots.txt
        return $this->generateDefault();
    }

    /**
     * Get default robots.txt directives.
     */
    public function getDefaultDirectives(): array
    {
        return [
            [
                'user_agent' => '*',
                'allow' => ['/'],
                'disallow' => [
                    '/admin',
                    '/admin/*',
                    '/api/*',
                    '/login',
                    '/register',
                    '/password/*',
                    '/email/*',
                    '/*.json$',
                ],
            ],
        ];
    }

    /**
     * Generate default robots.txt content.
     */
    private function generateDefault(): string
    {
        $lines = [];

        foreach ($this->getDefaultDirectives() as $directive) {
            $lines[] = 'User-agent: ' . $directive['user_agent'];

            if (isset($directive['allow'])) {
                foreach ((array) $directive['allow'] as $path) {
                    $lines[] = 'Allow: ' . $path;
                }
            }

            if (isset($directive['disallow'])) {
                foreach ((array) $directive['disallow'] as $path) {
                    $lines[] = 'Disallow: ' . $path;
                }
            }

            $lines[] = '';
        }

        return $this->appendSitemap(implode(PHP_EOL, $lines));
    }

    /**
     * Append sitemap URL to robots.txt content.
     */
    private function appendSitemap(string $content): string
    {
        $settings = SiteSettings::current();

        if (!$settings->sitemap_enabled) {
            return trim($content);
        }

        $sitemapUrl = url('/sitemap.xml');

        // Check if sitemap is already referenced
        if (stripos($content, 'sitemap:') !== false) {
            return trim($content);
        }

        return trim($content) . PHP_EOL . PHP_EOL . 'Sitemap: ' . $sitemapUrl;
    }
}
