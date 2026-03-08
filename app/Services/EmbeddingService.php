<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use OpenAI\Laravel\Facades\OpenAI;

/**
 * Handles creating and caching text embeddings via OpenAI API.
 */
class EmbeddingService
{
    protected string $model = 'text-embedding-3-small';
    protected int $dimensions = 1536;

    /**
     * Create an embedding for the given text.
     *
     * @param string $text The text to embed
     * @param bool $useCache Whether to cache the result
     * @return array The embedding vector
     */
    public function embed(string $text, bool $useCache = true): array
    {
        $cacheKey = 'embedding:' . md5($text);

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = OpenAI::embeddings()->create([
            'model' => $this->model,
            'input' => $text,
        ]);

        $embedding = $response->embeddings[0]->embedding;

        if ($useCache) {
            Cache::put($cacheKey, $embedding, now()->addDays(7));
        }

        return $embedding;
    }

    /**
     * Create embeddings for multiple texts.
     *
     * @param array $texts Array of texts to embed
     * @return array Array of embedding vectors
     */
    public function embedBatch(array $texts): array
    {
        $response = OpenAI::embeddings()->create([
            'model' => $this->model,
            'input' => $texts,
        ]);

        return array_map(fn ($e) => $e->embedding, $response->embeddings);
    }

    /**
     * Create a combined embedding from wizard data.
     */
    public function embedWizardData(array $wizardData): array
    {
        $text = $this->buildWizardText($wizardData);
        return $this->embed($text);
    }

    /**
     * Build a text representation of wizard data for embedding.
     */
    protected function buildWizardText(array $wizardData): string
    {
        $parts = [];

        if (!empty($wizardData['businessName'])) {
            $parts[] = "Business: " . $wizardData['businessName'];
        }

        if (!empty($wizardData['industry'])) {
            $parts[] = "Industry: " . $wizardData['industry'];
        }

        if (!empty($wizardData['description'])) {
            $parts[] = "Description: " . $wizardData['description'];
        }

        if (!empty($wizardData['targetAudience'])) {
            $parts[] = "Target audience: " . $wizardData['targetAudience'];
        }

        if (!empty($wizardData['services']) && is_array($wizardData['services'])) {
            $services = array_filter($wizardData['services']);
            if (!empty($services)) {
                $parts[] = "Services: " . implode(', ', $services);
            }
        }

        if (!empty($wizardData['primaryGoal'])) {
            $parts[] = "Goal: " . $wizardData['primaryGoal'];
        }

        if (!empty($wizardData['whyChooseUs'])) {
            $parts[] = "Value proposition: " . $wizardData['whyChooseUs'];
        }

        if (!empty($wizardData['companyStory'])) {
            $parts[] = "Story: " . $wizardData['companyStory'];
        }

        return implode('. ', $parts);
    }

    /**
     * Create embedding for a page context.
     */
    public function embedPageContext(string $pageSlug, array $wizardData): array
    {
        $text = "Page type: {$pageSlug}. " . $this->buildWizardText($wizardData);
        return $this->embed($text);
    }
}
