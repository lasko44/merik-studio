<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Services\LlmsService;
use Illuminate\Http\Response;

/**
 * Serves the llms.txt file for AI crawler guidance.
 */
class LlmsController extends Controller
{
    public function __invoke(LlmsService $llmsService): Response
    {
        $content = $llmsService->generate();

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
