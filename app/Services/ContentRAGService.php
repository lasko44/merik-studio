<?php

namespace App\Services;

use App\Models\ContentEmbedding;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

/**
 * RAG-based content generation service.
 * Uses vector embeddings to find relevant component examples and templates,
 * then uses them as context for AI content generation.
 */
class ContentRAGService
{
    public function __construct(
        protected EmbeddingService $embeddingService
    ) {}

    /**
     * Generate content for a page using RAG.
     * Results are cached for 24 hours to reduce API costs.
     */
    public function generatePageContent(string $pageSlug, array $wizardData): array
    {
        // Build cache key from page slug and relevant wizard data
        $cacheKey = $this->buildCacheKey($pageSlug, $wizardData);

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($pageSlug, $wizardData) {
            try {
                $industry = Arr::get($wizardData, 'industry', 'other');

                // Step 1: Embed the page context
                $contextEmbedding = $this->embeddingService->embedPageContext($pageSlug, $wizardData);

                // Step 2: Find similar templates for this page type
                $similarTemplates = ContentEmbedding::findSimilarTemplates($contextEmbedding, 3, $industry);

                // Step 3: Find similar component examples
                $similarComponents = ContentEmbedding::findSimilarComponents($contextEmbedding, 10, $industry);

                // Step 4: Find similar content examples
                $similarExamples = ContentEmbedding::findSimilarExamples($contextEmbedding, 5, $industry);

                // Step 5: Build context from retrieved results
                $ragContext = $this->buildRAGContext($similarTemplates, $similarComponents, $similarExamples);

                // Step 6: Generate content using AI with RAG context
                $content = $this->generateWithContext($pageSlug, $wizardData, $ragContext);

                Log::info("RAG generation for '{$pageSlug}'", [
                    'templates_found' => $similarTemplates->count(),
                    'components_found' => $similarComponents->count(),
                    'examples_found' => $similarExamples->count(),
                    'content_blocks' => count($content['content'] ?? []),
                ]);

                return $content;
            } catch (\Exception $e) {
                Log::error("RAG generation failed for '{$pageSlug}'", [
                    'error' => $e->getMessage(),
                ]);

                // Fallback to deterministic content builder
                return app(PageContentBuilder::class)->buildPageContent($pageSlug, $wizardData);
            }
        });
    }

    /**
     * Build a cache key for page content generation.
     * Includes page slug and hash of relevant wizard data.
     */
    protected function buildCacheKey(string $pageSlug, array $wizardData): string
    {
        // Extract only the fields that affect content generation
        $relevantData = [
            'businessName' => Arr::get($wizardData, 'businessName'),
            'industry' => Arr::get($wizardData, 'industry'),
            'description' => Arr::get($wizardData, 'description'),
            'services' => Arr::get($wizardData, 'services'),
            'targetAudience' => Arr::get($wizardData, 'targetAudience'),
            'companyStory' => Arr::get($wizardData, 'companyStory'),
            'whyChooseUs' => Arr::get($wizardData, 'whyChooseUs'),
            'achievements' => Arr::get($wizardData, 'achievements'),
            'processSteps' => Arr::get($wizardData, 'processSteps'),
            'faqItems' => Arr::get($wizardData, 'faqItems'),
            'testimonialNames' => Arr::get($wizardData, 'testimonialNames'),
            'primaryGoal' => Arr::get($wizardData, 'primaryGoal'),
            'contactEmail' => Arr::get($wizardData, 'contactEmail'),
            'contactPhone' => Arr::get($wizardData, 'contactPhone'),
            'contactAddress' => Arr::get($wizardData, 'contactAddress'),
        ];

        $dataHash = md5(json_encode($relevantData));

        return "rag:page:{$pageSlug}:{$dataHash}";
    }

    /**
     * Clear cached content for regeneration.
     */
    public function clearCache(array $wizardData): void
    {
        $pages = Arr::get($wizardData, 'pages', ['home', 'about', 'contact']);

        foreach ($pages as $pageSlug) {
            $cacheKey = $this->buildCacheKey($pageSlug, $wizardData);
            Cache::forget($cacheKey);
        }

        Log::info('Cleared RAG content cache', ['pages' => $pages]);
    }

    /**
     * Build RAG context from retrieved embeddings.
     */
    protected function buildRAGContext($templates, $components, $examples): string
    {
        $context = "=== RELEVANT TEMPLATES ===\n";
        foreach ($templates as $template) {
            $context .= "\nTemplate: {$template->name}\n";
            $context .= "Description: {$template->description}\n";
            $context .= "Example structure: " . json_encode($template->example_content, JSON_PRETTY_PRINT) . "\n";
        }

        $context .= "\n=== RELEVANT COMPONENTS ===\n";
        foreach ($components as $component) {
            $context .= "\nComponent: {$component->name}\n";
            $context .= "Description: {$component->description}\n";
            $context .= "Example: " . json_encode($component->example_content, JSON_PRETTY_PRINT) . "\n";
        }

        $context .= "\n=== RELEVANT CONTENT EXAMPLES ===\n";
        foreach ($examples as $example) {
            $context .= "\nExample ({$example->category}): {$example->name}\n";
            $context .= $example->description . "\n";
            $context .= "Content: " . json_encode($example->example_content, JSON_PRETTY_PRINT) . "\n";
        }

        return $context;
    }

    /**
     * Generate content using AI with RAG context.
     */
    protected function generateWithContext(string $pageSlug, array $wizardData, string $ragContext): array
    {
        $businessName = Arr::get($wizardData, 'businessName', 'Business');
        $description = Arr::get($wizardData, 'description', '');
        $services = array_filter(Arr::get($wizardData, 'services', []));
        $servicesText = !empty($services) ? implode(', ', $services) : 'our services';
        $companyStory = Arr::get($wizardData, 'companyStory', '');
        $whyChooseUs = Arr::get($wizardData, 'whyChooseUs', '');
        $achievements = Arr::get($wizardData, 'achievements', '');
        $processSteps = Arr::get($wizardData, 'processSteps', '');
        $faqItems = Arr::get($wizardData, 'faqItems', '');
        $testimonialNames = Arr::get($wizardData, 'testimonialNames', '');
        $targetAudience = Arr::get($wizardData, 'targetAudience', '');
        $primaryGoal = Arr::get($wizardData, 'primaryGoal', 'leads');
        $contactEmail = Arr::get($wizardData, 'contactEmail', '');
        $contactPhone = Arr::get($wizardData, 'contactPhone', '');
        $contactAddress = Arr::get($wizardData, 'contactAddress', '');

        $prompt = <<<PROMPT
You are building a {$pageSlug} page for "{$businessName}".

=== BUSINESS CONTEXT ===
Business: {$businessName}
Description: {$description}
Services: {$servicesText}
Target Audience: {$targetAudience}
Company Story: {$companyStory}
Why Choose Us: {$whyChooseUs}
Achievements: {$achievements}
Process: {$processSteps}
FAQ Info: {$faqItems}
Testimonial Names: {$testimonialNames}
Primary Goal: {$primaryGoal}
Contact Email: {$contactEmail}
Contact Phone: {$contactPhone}
Contact Address: {$contactAddress}

=== RETRIEVED CONTEXT (use these as examples) ===
{$ragContext}

=== YOUR TASK ===
Generate content blocks for the {$pageSlug} page following the patterns from the retrieved examples above.
Use the EXACT component types and prop structures shown in the examples.
Fill in the content based on the business context provided.

Return ONLY valid JSON in this exact format:
{
    "template": "landing|default|centered|full-width|sidebar|portfolio",
    "content": [
        {
            "id": "unique_id",
            "type": "component_type",
            "props": { ... component props ... }
        }
    ],
    "metaDescription": "SEO meta description for this page"
}

IMPORTANT:
- Use the component structures from the retrieved examples
- Fill props with REAL content based on the business info
- Create 4-6 content blocks for home/services pages
- Create 2-3 content blocks for contact/faq pages
- Make content compelling and specific to this business
- Use proper HTML in text content (<h2>, <h3>, <p>, <ul>, <li>, <strong>)

CONTACT INFO REQUIREMENTS:
- For contact pages: ALWAYS include a "contact-section" component with the EXACT contact info provided above
- The contact-section props MUST include: email="{$contactEmail}", phone="{$contactPhone}", address="{$contactAddress}"
- For home pages: Include contact info in the CTA or footer sections
- Never use placeholder contact info - use the REAL values provided
PROMPT;

        try {
            $response = OpenAI::chat()->create([
                'model' => config('ai.openai.model', 'gpt-4o'),
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert web designer creating page content. Return only valid JSON.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 4000,
            ]);

            $content = trim($response->choices[0]->message->content);

            // Clean JSON response
            $content = preg_replace('/^```json\s*/i', '', $content);
            $content = preg_replace('/^```\s*/i', '', $content);
            $content = preg_replace('/\s*```$/i', '', $content);

            $parsed = json_decode($content, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $this->validateAndEnhanceContent($parsed, $pageSlug, $wizardData);
            }

            Log::warning('RAG: Failed to parse AI response', [
                'page' => $pageSlug,
                'error' => json_last_error_msg(),
            ]);

        } catch (\Exception $e) {
            Log::error('RAG: AI generation failed', [
                'page' => $pageSlug,
                'error' => $e->getMessage(),
            ]);
        }

        // Fallback to deterministic content builder
        return app(PageContentBuilder::class)->buildPageContent($pageSlug, $wizardData);
    }

    /**
     * Validate and enhance the AI-generated content.
     */
    protected function validateAndEnhanceContent(array $content, string $pageSlug, array $wizardData): array
    {
        // Ensure content array exists
        if (empty($content['content'])) {
            return app(PageContentBuilder::class)->buildPageContent($pageSlug, $wizardData);
        }

        // Extract contact info from wizard data
        $contactEmail = Arr::get($wizardData, 'contactEmail', '');
        $contactPhone = Arr::get($wizardData, 'contactPhone', '');
        $contactAddress = Arr::get($wizardData, 'contactAddress', '');
        $businessName = Arr::get($wizardData, 'businessName', 'Business');

        // Ensure all blocks have IDs and inject contact info where needed
        foreach ($content['content'] as &$block) {
            if (empty($block['id'])) {
                $block['id'] = uniqid('block_');
            }

            // Inject real contact info into contact-section components
            if (Arr::get($block, 'type') === 'contact-section') {
                $block['props'] = array_merge($block['props'] ?? [], [
                    'email' => $contactEmail ?: Arr::get($block, 'props.email', ''),
                    'phone' => $contactPhone ?: Arr::get($block, 'props.phone', ''),
                    'address' => $contactAddress ?: Arr::get($block, 'props.address', ''),
                ]);
            }
        }

        // For contact pages, ensure there's a contact-section with real info
        if ($pageSlug === 'contact') {
            $hasContactSection = collect($content['content'])->contains(fn ($block) => Arr::get($block, 'type') === 'contact-section');

            if (!$hasContactSection && ($contactEmail || $contactPhone || $contactAddress)) {
                $content['content'][] = [
                    'id' => uniqid('block_'),
                    'type' => 'contact-section',
                    'props' => [
                        'title' => "Contact {$businessName}",
                        'email' => $contactEmail,
                        'phone' => $contactPhone,
                        'address' => $contactAddress,
                        'hours' => '',
                        'showForm' => true,
                    ],
                ];
            }
        }

        // Validate template
        $validTemplates = ['default', 'full-width', 'sidebar', 'sidebar-left', 'landing', 'centered', 'portfolio'];
        if (empty($content['template']) || !in_array($content['template'], $validTemplates)) {
            $content['template'] = $this->getDefaultTemplate($pageSlug);
        }

        return $content;
    }

    /**
     * Get default template for a page type.
     */
    protected function getDefaultTemplate(string $pageSlug): string
    {
        return match ($pageSlug) {
            'home' => 'landing',
            'contact' => 'centered',
            'portfolio', 'work', 'gallery' => 'portfolio',
            'blog' => 'sidebar',
            'services', 'features' => 'full-width',
            default => 'default',
        };
    }

    /**
     * Generate content for all pages.
     */
    public function generateAllPages(array $wizardData): array
    {
        $pages = Arr::get($wizardData, 'pages', ['home', 'about', 'contact']);
        $result = [];

        foreach ($pages as $pageSlug) {
            $result[$pageSlug] = $this->generatePageContent($pageSlug, $wizardData);
        }

        return $result;
    }
}
