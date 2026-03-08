<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSettings;
use App\Services\AiGenerationService;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Handles AI-powered content generation for site settings.
 */
class AiGenerationController extends Controller
{
    /**
     * Generate llms.txt content using AI.
     */
    public function generateLlmsTxt(AiGenerationService $service): JsonResponse
    {
        $this->authorize('settings.update');

        try {
            $settings = SiteSettings::current();
            $content = $service->generateLlmsTxt($settings);

            return response()->json([
                'success' => true,
                'content' => $content,
            ]);
        } catch (Exception $e) {
            Log::error('AI generation failed for llms.txt', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please check your OpenAI API key and try again.',
            ], 500);
        }
    }

    /**
     * Generate robots.txt content using AI.
     */
    public function generateRobotsTxt(AiGenerationService $service): JsonResponse
    {
        $this->authorize('settings.update');

        try {
            $settings = SiteSettings::current();
            $content = $service->generateRobotsTxt($settings);

            return response()->json([
                'success' => true,
                'content' => $content,
            ]);
        } catch (Exception $e) {
            Log::error('AI generation failed for robots.txt', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please check your OpenAI API key and try again.',
            ], 500);
        }
    }

    /**
     * Generate meta description using AI.
     */
    public function generateMetaDescription(AiGenerationService $service): JsonResponse
    {
        $this->authorize('settings.update');

        try {
            $settings = SiteSettings::current();
            $content = $service->generateMetaDescription($settings);

            return response()->json([
                'success' => true,
                'content' => $content,
            ]);
        } catch (Exception $e) {
            Log::error('AI generation failed for meta description', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please check your OpenAI API key and try again.',
            ], 500);
        }
    }

    /**
     * Generate meta keywords using AI.
     */
    public function generateMetaKeywords(AiGenerationService $service): JsonResponse
    {
        $this->authorize('settings.update');

        try {
            $settings = SiteSettings::current();
            $content = $service->generateMetaKeywords($settings);

            return response()->json([
                'success' => true,
                'content' => $content,
            ]);
        } catch (Exception $e) {
            Log::error('AI generation failed for meta keywords', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please check your OpenAI API key and try again.',
            ], 500);
        }
    }
}
