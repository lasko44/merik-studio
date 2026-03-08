<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Services\SitemapService;
use Illuminate\Http\Response;

/**
 * Serves the XML sitemap for search engine crawlers.
 */
class SitemapController extends Controller
{
    public function __invoke(SitemapService $sitemapService): Response
    {
        $xml = $sitemapService->generate();

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
