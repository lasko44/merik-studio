<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Services\RobotsService;
use Illuminate\Http\Response;

/**
 * Serves the robots.txt file for search engine crawlers.
 */
class RobotsController extends Controller
{
    public function __invoke(RobotsService $robotsService): Response
    {
        $content = $robotsService->generate();

        return response($content, 200, [
            'Content-Type' => 'text/plain',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
