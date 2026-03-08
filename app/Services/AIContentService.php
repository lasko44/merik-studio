<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

/**
 * Handles AI-powered content generation for the setup wizard with
 * intelligent template and component selection (RAG-like approach).
 */
class AIContentService
{
    /**
     * Template definitions for AI decision making.
     */
    protected array $templates = [
        'landing' => [
            'name' => 'Landing Page',
            'description' => 'Full-width hero with features section, content, and CTA. Best for: home pages, marketing pages, conversion-focused pages.',
            'sections' => ['hero', 'features', 'content', 'cta'],
            'best_for' => ['home', 'landing', 'marketing'],
        ],
        'full-width' => [
            'name' => 'Full Width',
            'description' => 'Edge-to-edge immersive layout. Best for: hero-focused pages, visual showcase, impactful statements.',
            'sections' => ['content'],
            'best_for' => ['services', 'showcase'],
        ],
        'default' => [
            'name' => 'Default',
            'description' => 'Standard contained layout with max-width. Best for: about pages, general content, text-heavy pages.',
            'sections' => ['content'],
            'best_for' => ['about', 'faq', 'general'],
        ],
        'sidebar' => [
            'name' => 'Sidebar Right',
            'description' => 'Two-column with sidebar on right. Best for: services with quick links, content with CTAs, pages needing secondary info.',
            'sections' => ['content', 'sidebar'],
            'best_for' => ['services', 'blog'],
        ],
        'centered' => [
            'name' => 'Centered',
            'description' => 'Small centered layout. Best for: contact forms, focused content, simple pages.',
            'sections' => ['content'],
            'best_for' => ['contact', 'thank-you'],
        ],
        'portfolio' => [
            'name' => 'Portfolio',
            'description' => 'Grid showcase layout. Best for: work samples, galleries, project showcases.',
            'sections' => ['header', 'gallery', 'content'],
            'best_for' => ['portfolio', 'work', 'gallery', 'projects'],
        ],
    ];

    /**
     * Component definitions for AI content generation.
     */
    protected array $components = [
        'hero' => [
            'description' => 'Large hero section with heading, subheading, and CTA buttons. Use for page headers.',
            'props' => [
                'heading' => 'Main headline (compelling, benefit-focused)',
                'subheading' => 'Supporting text (2-3 sentences max)',
                'primaryButtonText' => 'Main CTA button text',
                'primaryButtonLink' => 'Main CTA link (e.g., /contact)',
                'secondaryButtonText' => 'Secondary button text (optional)',
                'secondaryButtonLink' => 'Secondary button link',
                'alignment' => 'center|left|right',
            ],
        ],
        'features' => [
            'description' => 'Grid of features/services with icons. Use to highlight key benefits or services.',
            'props' => [
                'columns' => '2|3|4 (number of columns)',
                'features' => 'Array of {icon, title, description}',
            ],
            'icons' => ['star', 'heart', 'check', 'shield', 'zap', 'globe', 'users', 'target', 'trending-up', 'award', 'clock', 'phone', 'mail', 'map-pin'],
        ],
        'text' => [
            'description' => 'Rich text content block. Use for paragraphs, lists, and formatted content.',
            'props' => [
                'content' => 'HTML content with h2, h3, p, ul, li, strong tags',
                'alignment' => 'left|center|right',
            ],
        ],
        'call-to-action' => [
            'description' => 'Prominent CTA section. Use at end of pages to drive action.',
            'props' => [
                'heading' => 'CTA headline',
                'description' => 'Supporting text',
                'buttonText' => 'Button text',
                'buttonLink' => 'Button link',
                'backgroundColor' => 'primary|secondary|accent|dark',
            ],
        ],
        'testimonials' => [
            'description' => 'Customer testimonials/reviews. Use to build trust and social proof.',
            'props' => [
                'layout' => 'grid|carousel',
                'testimonials' => 'Array of {name, role, content, avatar: null}',
            ],
        ],
        'contact-section' => [
            'description' => 'Contact information with optional form. Use on contact pages.',
            'props' => [
                'title' => 'Section title',
                'email' => 'Contact email',
                'phone' => 'Contact phone',
                'address' => 'Physical address',
                'hours' => 'Business hours',
                'showForm' => 'true|false',
            ],
        ],
        'image-gallery' => [
            'description' => 'Grid of images with lightbox. Use for visual showcases.',
            'props' => [
                'images' => 'Array of {src, alt, caption}',
                'columns' => '2|3|4|5',
                'gap' => 'sm|md|lg',
            ],
        ],
        'two-column-layout' => [
            'description' => 'Flexible two-column layout. Use for side-by-side content.',
            'props' => [
                'leftWidth' => '1/2|1/3|2/3',
                'rightWidth' => '1/2|1/3|2/3',
                'gap' => 'sm|md|lg',
                'leftContent' => 'Array of content blocks',
                'rightContent' => 'Array of content blocks',
            ],
        ],
        'pricing' => [
            'description' => 'Pricing table with plans. Use for pricing pages.',
            'props' => [
                'plans' => 'Array of {name, price, period, description, features[], featured}',
            ],
        ],
        // Sidebar-specific components
        'sidebar-card' => [
            'description' => 'Simple card for sidebar. Use for quick info or summaries.',
            'props' => [
                'title' => 'Card title',
                'content' => 'HTML content',
                'variant' => 'default|bordered|filled',
            ],
        ],
        'sidebar-cta' => [
            'description' => 'CTA card for sidebar. Use to drive secondary actions.',
            'props' => [
                'title' => 'CTA title',
                'description' => 'Short description',
                'buttonText' => 'Button text',
                'buttonLink' => 'Button link',
                'variant' => 'primary|secondary',
            ],
        ],
        'sidebar-contact' => [
            'description' => 'Contact info card for sidebar.',
            'props' => [
                'title' => 'Card title',
                'email' => 'Email',
                'phone' => 'Phone',
                'address' => 'Address',
                'showIcons' => 'true|false',
            ],
        ],
        'sidebar-links' => [
            'description' => 'Navigation links for sidebar.',
            'props' => [
                'title' => 'Section title',
                'links' => 'Array of {text, url}',
                'style' => 'list|buttons',
            ],
        ],
    ];

    /**
     * Generate complete site content based on wizard data.
     */
    public function generateSiteContent(array $wizardData): array
    {
        Log::info('Starting AI content generation', [
            'business_name' => Arr::get($wizardData, 'businessName'),
            'pages' => Arr::get($wizardData, 'pages'),
        ]);

        $prompt = $this->buildSiteContentPrompt($wizardData);
        $response = $this->callAI($prompt, 8000);

        Log::info('AI response received', [
            'response_length' => strlen($response),
        ]);

        $result = $this->parseSiteContentResponse($response, $wizardData);

        Log::info('AI content parsed', [
            'pages_generated' => count($result['pages'] ?? []),
            'page_details' => array_map(function ($page) {
                return [
                    'slug' => $page['slug'] ?? 'unknown',
                    'content_blocks' => count($page['content'] ?? []),
                    'template' => $page['template'] ?? 'none',
                ];
            }, $result['pages'] ?? []),
        ]);

        return $result;
    }

    /**
     * Generate a color palette based on mood and optional primary color.
     */
    public function generateColorPalette(string $mood, ?string $primaryColor = null): array
    {
        $moodColors = config('ai.color_moods.' . $mood, config('ai.color_moods.professional'));

        if ($primaryColor) {
            $moodColors['primary'] = $primaryColor;
        }

        return $moodColors;
    }

    /**
     * Get recommended template for a page based on context.
     */
    public function getRecommendedTemplate(string $pageSlug, string $industry, string $primaryGoal): string
    {
        // Page-specific template recommendations
        $pageTemplates = [
            'home' => 'landing',
            'about' => 'default',
            'services' => 'full-width',
            'contact' => 'centered',
            'faq' => 'default',
            'testimonials' => 'default',
            'portfolio' => 'portfolio',
            'work' => 'portfolio',
            'gallery' => 'portfolio',
            'team' => 'default',
            'pricing' => 'default',
            'blog' => 'sidebar',
        ];

        // Industry-specific overrides
        $industryOverrides = [
            'portfolio' => [
                'home' => 'landing',
                'work' => 'portfolio',
            ],
            'agency' => [
                'home' => 'landing',
                'work' => 'portfolio',
                'services' => 'sidebar',
            ],
            'consultant' => [
                'services' => 'sidebar',
            ],
            'ecommerce' => [
                'home' => 'landing',
            ],
        ];

        // Goal-specific overrides
        $goalOverrides = [
            'sales' => [
                'home' => 'landing',
                'services' => 'full-width',
            ],
            'leads' => [
                'home' => 'landing',
                'contact' => 'centered',
            ],
        ];

        // Apply overrides in order of specificity
        $template = $pageTemplates[$pageSlug] ?? 'default';

        if (isset($industryOverrides[$industry][$pageSlug])) {
            $template = $industryOverrides[$industry][$pageSlug];
        }

        if (isset($goalOverrides[$primaryGoal][$pageSlug])) {
            $template = $goalOverrides[$primaryGoal][$pageSlug];
        }

        return $template;
    }

    /**
     * Call OpenAI API with the given prompt.
     */
    protected function callAI(string $prompt, int $maxTokens = 4096): string
    {
        $response = OpenAI::chat()->create([
            'model' => config('ai.openai.model', 'gpt-4o'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->getSystemPrompt(),
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => config('ai.openai.temperature', 0.7),
            'max_tokens' => $maxTokens,
        ]);

        return trim(Arr::get($response, 'choices.0.message.content', ''));
    }

    /**
     * Get the system prompt for the AI.
     */
    protected function getSystemPrompt(): string
    {
        return <<<'SYSTEM'
You are an expert website designer and copywriter who creates compelling, conversion-focused websites. You excel at:

1. TEMPLATE SELECTION: Choosing the perfect page template based on page purpose and content needs
2. CONTENT ARCHITECTURE: Structuring pages with the right components in the right order
3. COPYWRITING: Writing professional copy that speaks to the target audience
4. VISUAL HIERARCHY: Creating diverse, well-designed pages that don't all look the same
5. CONVERSION OPTIMIZATION: Including strategic CTAs and trust elements

You understand that different pages need different layouts:
- Home pages need impact (landing template with hero + features)
- About pages need storytelling (default template with text blocks)
- Services pages need clarity (full-width or sidebar for detailed info)
- Contact pages need simplicity (centered template with form)
- Portfolio pages need visual focus (portfolio template with gallery)

You MUST respond with valid JSON only. No markdown, no code blocks, just pure JSON.
SYSTEM;
    }

    /**
     * Build the comprehensive prompt for generating site content.
     */
    protected function buildSiteContentPrompt(array $data): string
    {
        $businessName = Arr::get($data, 'businessName', 'Business');
        $industry = Arr::get($data, 'industry', 'other');
        $description = Arr::get($data, 'description', '');
        $targetAudience = Arr::get($data, 'targetAudience', '');
        $tagline = Arr::get($data, 'tagline', '');
        $yearsInBusiness = Arr::get($data, 'yearsInBusiness', '');
        $location = Arr::get($data, 'location', '');
        $primaryGoal = Arr::get($data, 'primaryGoal', 'leads');
        $secondaryGoals = Arr::get($data, 'secondaryGoals', []);
        $callToAction = Arr::get($data, 'callToAction', '');
        $pages = Arr::get($data, 'pages', ['home', 'about', 'contact']);
        $features = Arr::get($data, 'features', []);

        // Content fields
        $services = Arr::get($data, 'services', []);
        $companyStory = Arr::get($data, 'companyStory', '');
        $achievements = Arr::get($data, 'achievements', '');
        $whyChooseUs = Arr::get($data, 'whyChooseUs', '');
        $processSteps = Arr::get($data, 'processSteps', '');
        $faqItems = Arr::get($data, 'faqItems', '');
        $testimonialNames = Arr::get($data, 'testimonialNames', '');

        $contactEmail = Arr::get($data, 'contactEmail', '');
        $contactPhone = Arr::get($data, 'contactPhone', '');
        $contactAddress = Arr::get($data, 'contactAddress', '');

        // Format arrays
        $servicesText = is_array($services) ? implode("\n- ", array_filter($services)) : $services;
        $secondaryGoalsText = is_array($secondaryGoals) ? implode(', ', $secondaryGoals) : '';
        $pagesText = implode(', ', $pages);
        $featuresText = is_array($features) ? implode(', ', array_keys(array_filter($features))) : '';

        $experienceText = match ($yearsInBusiness) {
            'new' => 'newly established',
            '1-2' => '1-2 years of experience',
            '3-5' => '3-5 years of experience',
            '5-10' => '5-10 years of experience',
            '10-20' => '10-20 years of experience',
            '20+' => 'over 20 years of experience',
            default => ''
        };

        // Get recommended templates for each page
        $templateRecommendations = [];
        foreach ($pages as $page) {
            $templateRecommendations[$page] = $this->getRecommendedTemplate($page, $industry, $primaryGoal);
        }
        $templateRecsJson = json_encode($templateRecommendations, JSON_PRETTY_PRINT);

        // Build template reference
        $templateRef = $this->buildTemplateReference();

        // Build component reference
        $componentRef = $this->buildComponentReference();

        return <<<PROMPT
Create a complete, professional website for this business. Generate diverse, well-designed pages using different templates and components.

═══════════════════════════════════════════════════════════════════════
BUSINESS INFORMATION
═══════════════════════════════════════════════════════════════════════

Business Name: {$businessName}
Industry: {$industry}
Location: {$location}
Experience: {$experienceText}
Tagline: {$tagline}

DESCRIPTION:
{$description}

TARGET AUDIENCE:
{$targetAudience}

SERVICES/PRODUCTS:
- {$servicesText}

COMPANY STORY:
{$companyStory}

WHY CHOOSE US:
{$whyChooseUs}

ACHIEVEMENTS:
{$achievements}

WORKING PROCESS:
{$processSteps}

FAQ INFORMATION:
{$faqItems}

TESTIMONIAL SOURCES:
{$testimonialNames}

CONTACT INFO:
- Email: {$contactEmail}
- Phone: {$contactPhone}
- Address: {$contactAddress}

GOALS:
- Primary: {$primaryGoal}
- Secondary: {$secondaryGoalsText}
- Desired CTA: {$callToAction}

ENABLED FEATURES: {$featuresText}

═══════════════════════════════════════════════════════════════════════
AVAILABLE TEMPLATES (Choose the best one for each page)
═══════════════════════════════════════════════════════════════════════

{$templateRef}

RECOMMENDED TEMPLATES FOR YOUR PAGES:
{$templateRecsJson}

═══════════════════════════════════════════════════════════════════════
AVAILABLE COMPONENTS (Use these to build page content)
═══════════════════════════════════════════════════════════════════════

{$componentRef}

═══════════════════════════════════════════════════════════════════════
PAGES TO CREATE: {$pagesText}
═══════════════════════════════════════════════════════════════════════

Generate JSON with this structure:

{
    "settings": {
        "tagline": "Compelling tagline (max 60 chars)",
        "metaDescription": "SEO meta description (150-160 chars)"
    },
    "pages": [
        {
            "slug": "home",
            "title": "Home",
            "template": "landing",
            "metaDescription": "Page-specific meta description",
            "content": [
                {
                    "type": "hero",
                    "props": {
                        "heading": "Compelling headline",
                        "subheading": "Supporting text",
                        "primaryButtonText": "CTA text",
                        "primaryButtonLink": "/contact",
                        "secondaryButtonText": "Learn More",
                        "secondaryButtonLink": "/about",
                        "alignment": "center"
                    }
                }
                // ... more components
            ],
            "sidebarContent": [] // Only for sidebar/sidebar-left templates
        }
    ],
    "navigation": [
        {"title": "About", "path": "/about", "order": 1}
    ]
}

═══════════════════════════════════════════════════════════════════════
DESIGN RULES - FOLLOW THESE EXACTLY
═══════════════════════════════════════════════════════════════════════

1. DIVERSITY: Use different templates for different pages:
   - HOME: Use "landing" template
   - ABOUT: Use "default" template
   - SERVICES/FEATURES: Use "full-width" template
   - CONTACT: Use "centered" template
   - FAQ: Use "default" template
   - PORTFOLIO/WORK: Use "portfolio" template
   - BLOG: Use "sidebar" template

2. CONTENT BLOCKS PER PAGE (MINIMUM REQUIREMENTS):
   - HOME: 5-6 blocks (hero → features → text story → testimonials → text achievements → CTA)
   - ABOUT: 4-5 blocks (hero → text story → features values → text achievements → CTA)
   - SERVICES/FEATURES: 5-6 blocks (hero → features grid → text process → testimonials → CTA)
   - CONTACT: 2-3 blocks (hero → contact-section)
   - FAQ: 2-3 blocks (hero → text with Q&A formatted)
   - TESTIMONIALS: 2-3 blocks (hero → testimonials)
   - PRICING: 3-4 blocks (hero → text intro → pricing table → CTA)

3. FEATURES COMPONENT: Always use 3-4 features with relevant icons:
   - star, heart, check, shield, zap, globe, users, target, trending-up, award, clock

4. SIDEBAR CONTENT: For sidebar templates, include:
   - sidebar-cta with a compelling offer
   - sidebar-contact with business info
   - sidebar-links for quick navigation

5. TEXT CONTENT: Use proper HTML structure:
   - <h2> for section headings
   - <h3> for subheadings
   - <p> for paragraphs
   - <ul><li> for lists
   - <strong> for emphasis

6. TESTIMONIALS: Create realistic testimonials based on testimonialNames. Include:
   - name, role/company, detailed content (2-3 sentences), avatar: null

7. FAQ FORMAT: In text blocks, format as:
   <h3>Question here?</h3>
   <p>Detailed answer here.</p>

8. CTAs: Make them action-oriented based on primaryGoal:
   - leads: "Get Free Quote", "Schedule Consultation"
   - sales: "Shop Now", "See Pricing"
   - information: "Learn More", "Read Our Blog"

9. USE ALL PROVIDED INFO: Reference services, story, achievements, location, experience throughout.

10. PERSONALIZATION: Use business name naturally, address target audience directly.

Generate content for ALL pages: {$pagesText}

Respond with ONLY valid JSON. No markdown code blocks.
PROMPT;
    }

    /**
     * Build template reference documentation for the AI.
     */
    protected function buildTemplateReference(): string
    {
        $lines = [];
        foreach ($this->templates as $slug => $template) {
            $lines[] = "• {$slug}: {$template['description']}";
        }
        return implode("\n", $lines);
    }

    /**
     * Build component reference documentation for the AI.
     */
    protected function buildComponentReference(): string
    {
        $lines = [];
        foreach ($this->components as $slug => $component) {
            $propsStr = json_encode($component['props'], JSON_PRETTY_PRINT);
            $lines[] = "• {$slug}: {$component['description']}";
            $lines[] = "  Props: {$propsStr}";
            if (isset($component['icons'])) {
                $lines[] = "  Icons: " . implode(', ', $component['icons']);
            }
            $lines[] = "";
        }
        return implode("\n", $lines);
    }

    /**
     * Parse the AI response for site content.
     */
    protected function parseSiteContentResponse(string $response, array $wizardData): array
    {
        Log::debug('AI raw response preview', [
            'first_500_chars' => substr($response, 0, 500),
            'last_200_chars' => substr($response, -200),
        ]);

        // Clean the response
        $response = preg_replace('/^```json\s*/i', '', $response);
        $response = preg_replace('/^```\s*/i', '', $response);
        $response = preg_replace('/\s*```$/i', '', $response);
        $response = trim($response);

        $parsed = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('Failed to parse AI response as JSON', [
                'json_error' => json_last_error_msg(),
                'response_preview' => substr($response, 0, 500),
            ]);
            return $this->getFallbackContent($wizardData);
        }

        Log::info('Successfully parsed AI JSON response', [
            'has_settings' => isset($parsed['settings']),
            'has_pages' => isset($parsed['pages']),
            'pages_count' => count($parsed['pages'] ?? []),
        ]);

        // Validate and enhance parsed content
        return $this->validateAndEnhanceContent($parsed, $wizardData);
    }

    /**
     * Validate and enhance the AI-generated content.
     */
    protected function validateAndEnhanceContent(array $content, array $wizardData): array
    {
        $pages = Arr::get($content, 'pages', []);
        $requestedPages = Arr::get($wizardData, 'pages', ['home']);
        $industry = Arr::get($wizardData, 'industry', 'other');
        $primaryGoal = Arr::get($wizardData, 'primaryGoal', 'leads');

        // Ensure all requested pages exist and have valid templates
        $existingPageSlugs = array_column($pages, 'slug');

        foreach ($requestedPages as $pageSlug) {
            if (!in_array($pageSlug, $existingPageSlugs)) {
                // Add missing page with default content
                $pages[] = $this->getDefaultPageContent($pageSlug, [
                    'businessName' => Arr::get($wizardData, 'businessName', 'Business'),
                    'description' => Arr::get($wizardData, 'description', ''),
                    'services' => Arr::get($wizardData, 'services', []),
                    'whyChooseUs' => Arr::get($wizardData, 'whyChooseUs', ''),
                    'companyStory' => Arr::get($wizardData, 'companyStory', ''),
                    'contactEmail' => Arr::get($wizardData, 'contactEmail', ''),
                    'contactPhone' => Arr::get($wizardData, 'contactPhone', ''),
                    'contactAddress' => Arr::get($wizardData, 'contactAddress', ''),
                ]);
            }
        }

        // Ensure each page has a valid template
        foreach ($pages as &$page) {
            $pageSlug = Arr::get($page, 'slug', '');
            if (empty(Arr::get($page, 'template'))) {
                $page['template'] = $this->getRecommendedTemplate($pageSlug, $industry, $primaryGoal);
            }

            // Ensure content array exists and has blocks
            if (empty(Arr::get($page, 'content'))) {
                $page['content'] = [
                    [
                        'type' => 'text',
                        'props' => [
                            'content' => '<h2>' . ucfirst($pageSlug) . '</h2><p>Content coming soon.</p>',
                            'alignment' => 'left',
                        ],
                    ],
                ];
            }
        }

        $content['pages'] = $pages;

        return $content;
    }

    /**
     * Get fallback content if AI generation fails.
     */
    protected function getFallbackContent(array $wizardData): array
    {
        $businessName = Arr::get($wizardData, 'businessName', 'Your Business');
        $description = Arr::get($wizardData, 'description', 'Welcome to our website.');
        $tagline = Arr::get($wizardData, 'tagline', '');
        $pages = Arr::get($wizardData, 'pages', ['home', 'about', 'contact']);
        $services = array_filter(Arr::get($wizardData, 'services', []));
        $whyChooseUs = Arr::get($wizardData, 'whyChooseUs', '');
        $companyStory = Arr::get($wizardData, 'companyStory', '');
        $contactEmail = Arr::get($wizardData, 'contactEmail', '');
        $contactPhone = Arr::get($wizardData, 'contactPhone', '');
        $contactAddress = Arr::get($wizardData, 'contactAddress', '');
        $industry = Arr::get($wizardData, 'industry', 'other');
        $primaryGoal = Arr::get($wizardData, 'primaryGoal', 'leads');

        $generatedPages = [];
        $navigation = [];
        $order = 0;

        foreach ($pages as $pageSlug) {
            $pageContent = $this->getDefaultPageContent($pageSlug, [
                'businessName' => $businessName,
                'description' => $description,
                'services' => $services,
                'whyChooseUs' => $whyChooseUs,
                'companyStory' => $companyStory,
                'contactEmail' => $contactEmail,
                'contactPhone' => $contactPhone,
                'contactAddress' => $contactAddress,
            ]);

            // Add recommended template
            $pageContent['template'] = $this->getRecommendedTemplate($pageSlug, $industry, $primaryGoal);

            $generatedPages[] = $pageContent;

            if ($pageSlug !== 'home') {
                $navigation[] = [
                    'title' => ucfirst($pageSlug),
                    'path' => '/' . $pageSlug,
                    'order' => $order,
                ];
            }
            $order++;
        }

        return [
            'settings' => [
                'tagline' => $tagline ?: 'Professional solutions for your needs',
                'metaDescription' => substr($description, 0, 160) ?: "Welcome to {$businessName}. We provide professional services tailored to your needs.",
            ],
            'pages' => $generatedPages,
            'navigation' => $navigation,
        ];
    }

    /**
     * Get default content for a specific page type with template.
     */
    protected function getDefaultPageContent(string $pageSlug, array $context): array
    {
        $businessName = Arr::get($context, 'businessName', 'Business');
        $description = Arr::get($context, 'description', '');
        $services = Arr::get($context, 'services', []);
        $whyChooseUs = Arr::get($context, 'whyChooseUs', '');
        $companyStory = Arr::get($context, 'companyStory', '');
        $contactEmail = Arr::get($context, 'contactEmail', '');
        $contactPhone = Arr::get($context, 'contactPhone', '');
        $contactAddress = Arr::get($context, 'contactAddress', '');

        // Build features from services
        $serviceFeatures = [];
        $icons = ['star', 'shield', 'zap', 'check', 'target', 'award'];
        foreach (array_slice($services, 0, 6) as $index => $service) {
            if (!empty($service)) {
                $parts = explode(' - ', $service, 2);
                $serviceFeatures[] = [
                    'icon' => $icons[$index % count($icons)],
                    'title' => $parts[0],
                    'description' => $parts[1] ?? 'Professional service tailored to your needs.',
                ];
            }
        }

        if (empty($serviceFeatures)) {
            $serviceFeatures = [
                ['icon' => 'star', 'title' => 'Quality Service', 'description' => 'We deliver exceptional results every time.'],
                ['icon' => 'users', 'title' => 'Expert Team', 'description' => 'Our professionals bring years of experience.'],
                ['icon' => 'heart', 'title' => 'Customer Focus', 'description' => 'Your satisfaction is our top priority.'],
            ];
        }

        $defaults = [
            'home' => [
                'slug' => 'home',
                'title' => 'Home',
                'template' => 'landing',
                'metaDescription' => "Welcome to {$businessName} - {$description}",
                'content' => [
                    [
                        'type' => 'hero',
                        'props' => [
                            'heading' => "Welcome to {$businessName}",
                            'subheading' => $description ?: 'We provide professional solutions tailored to your needs.',
                            'primaryButtonText' => 'Get Started',
                            'primaryButtonLink' => '/contact',
                            'secondaryButtonText' => 'Learn More',
                            'secondaryButtonLink' => '/about',
                            'alignment' => 'center',
                        ],
                    ],
                    [
                        'type' => 'features',
                        'props' => [
                            'columns' => min(count($serviceFeatures), 3),
                            'features' => array_slice($serviceFeatures, 0, 3),
                        ],
                    ],
                    [
                        'type' => 'text',
                        'props' => [
                            'content' => $whyChooseUs
                                ? "<h2>Why Choose {$businessName}?</h2><p>{$whyChooseUs}</p>"
                                : "<h2>Why Choose {$businessName}?</h2><p>We are dedicated to providing exceptional service and delivering results that exceed your expectations. Our team of experienced professionals is committed to your success.</p>",
                            'alignment' => 'left',
                        ],
                    ],
                    [
                        'type' => 'testimonials',
                        'props' => [
                            'layout' => 'grid',
                            'testimonials' => [
                                ['name' => 'Satisfied Customer', 'role' => 'Client', 'content' => "Working with {$businessName} has been an excellent experience. They delivered exactly what we needed.", 'avatar' => null],
                                ['name' => 'Happy Client', 'role' => 'Business Owner', 'content' => 'Professional, responsive, and delivered exceptional results. Highly recommend!', 'avatar' => null],
                            ],
                        ],
                    ],
                    [
                        'type' => 'call-to-action',
                        'props' => [
                            'heading' => 'Ready to Get Started?',
                            'description' => 'Contact us today to learn how we can help you achieve your goals.',
                            'buttonText' => 'Contact Us',
                            'buttonLink' => '/contact',
                            'backgroundColor' => 'primary',
                        ],
                    ],
                ],
            ],
            'about' => [
                'slug' => 'about',
                'title' => 'About Us',
                'template' => 'default',
                'metaDescription' => "Learn more about {$businessName} - our story, mission, and values.",
                'content' => [
                    [
                        'type' => 'hero',
                        'props' => [
                            'heading' => "About {$businessName}",
                            'subheading' => 'Our story, mission, and commitment to excellence.',
                            'primaryButtonText' => 'Contact Us',
                            'primaryButtonLink' => '/contact',
                            'alignment' => 'center',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'props' => [
                            'content' => $companyStory
                                ? "<h2>Our Story</h2><p>{$companyStory}</p>"
                                : "<h2>Our Story</h2><p>{$description}</p><p>We are dedicated to providing excellent service to our customers. Our team of experienced professionals is committed to delivering results that exceed expectations.</p>",
                            'alignment' => 'left',
                        ],
                    ],
                    [
                        'type' => 'features',
                        'props' => [
                            'columns' => 3,
                            'features' => [
                                ['icon' => 'target', 'title' => 'Our Mission', 'description' => 'To deliver exceptional value and service to every customer.'],
                                ['icon' => 'award', 'title' => 'Our Values', 'description' => 'Integrity, excellence, and customer satisfaction drive everything we do.'],
                                ['icon' => 'trending-up', 'title' => 'Our Vision', 'description' => 'To be the industry leader in quality and innovation.'],
                            ],
                        ],
                    ],
                    [
                        'type' => 'call-to-action',
                        'props' => [
                            'heading' => 'Want to Work With Us?',
                            'description' => 'Get in touch to discuss how we can help you.',
                            'buttonText' => 'Contact Us',
                            'buttonLink' => '/contact',
                            'backgroundColor' => 'primary',
                        ],
                    ],
                ],
            ],
            'services' => [
                'slug' => 'services',
                'title' => 'Our Services',
                'template' => 'full-width',
                'metaDescription' => "Professional services offered by {$businessName}.",
                'content' => [
                    [
                        'type' => 'hero',
                        'props' => [
                            'heading' => 'Our Services',
                            'subheading' => "Professional solutions to help you succeed.",
                            'primaryButtonText' => 'Get a Quote',
                            'primaryButtonLink' => '/contact',
                            'alignment' => 'center',
                        ],
                    ],
                    [
                        'type' => 'features',
                        'props' => [
                            'columns' => min(count($serviceFeatures), 3),
                            'features' => $serviceFeatures,
                        ],
                    ],
                    [
                        'type' => 'text',
                        'props' => [
                            'content' => "<h2>How We Work</h2><p>We follow a proven process to ensure your success. From initial consultation to final delivery, we're with you every step of the way.</p><ul><li><strong>Step 1:</strong> Consultation - We understand your needs</li><li><strong>Step 2:</strong> Planning - We create a customized solution</li><li><strong>Step 3:</strong> Execution - We deliver with excellence</li><li><strong>Step 4:</strong> Support - We provide ongoing assistance</li></ul>",
                            'alignment' => 'left',
                        ],
                    ],
                    [
                        'type' => 'call-to-action',
                        'props' => [
                            'heading' => 'Ready to Get Started?',
                            'description' => 'Contact us today for a free consultation.',
                            'buttonText' => 'Contact Us',
                            'buttonLink' => '/contact',
                            'backgroundColor' => 'primary',
                        ],
                    ],
                ],
            ],
            'contact' => [
                'slug' => 'contact',
                'title' => 'Contact Us',
                'template' => 'centered',
                'metaDescription' => "Get in touch with {$businessName}. We'd love to hear from you.",
                'content' => [
                    [
                        'type' => 'hero',
                        'props' => [
                            'heading' => 'Get in Touch',
                            'subheading' => "We'd love to hear from you. Reach out today!",
                            'alignment' => 'center',
                        ],
                    ],
                    [
                        'type' => 'contact-section',
                        'props' => [
                            'title' => 'Contact Us',
                            'email' => $contactEmail,
                            'phone' => $contactPhone,
                            'address' => $contactAddress,
                            'showForm' => true,
                        ],
                    ],
                ],
            ],
            'faq' => [
                'slug' => 'faq',
                'title' => 'FAQ',
                'template' => 'default',
                'metaDescription' => "Frequently asked questions about {$businessName}.",
                'content' => [
                    [
                        'type' => 'hero',
                        'props' => [
                            'heading' => 'Frequently Asked Questions',
                            'subheading' => 'Find answers to common questions below.',
                            'alignment' => 'center',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'props' => [
                            'content' => "<h3>What services do you offer?</h3><p>We offer a comprehensive range of professional services tailored to your needs. Contact us to learn more about how we can help.</p><h3>How can I get started?</h3><p>Simply reach out through our contact form or give us a call. We'll schedule a consultation to discuss your needs.</p><h3>What are your hours of operation?</h3><p>We're available Monday through Friday, 9 AM to 5 PM. Feel free to leave a message outside these hours.</p><h3>Do you offer free consultations?</h3><p>Yes! We offer free initial consultations to understand your needs and provide recommendations.</p>",
                            'alignment' => 'left',
                        ],
                    ],
                ],
            ],
            'testimonials' => [
                'slug' => 'testimonials',
                'title' => 'Testimonials',
                'template' => 'default',
                'metaDescription' => "What our customers say about {$businessName}.",
                'content' => [
                    [
                        'type' => 'hero',
                        'props' => [
                            'heading' => 'What Our Customers Say',
                            'subheading' => 'Real feedback from real customers.',
                            'alignment' => 'center',
                        ],
                    ],
                    [
                        'type' => 'testimonials',
                        'props' => [
                            'layout' => 'grid',
                            'testimonials' => [
                                ['name' => 'John Smith', 'role' => 'Business Owner', 'content' => "Working with {$businessName} has been an excellent experience. They delivered exactly what we needed.", 'avatar' => null],
                                ['name' => 'Sarah Johnson', 'role' => 'Marketing Director', 'content' => 'Professional, responsive, and delivered exceptional results. Highly recommend!', 'avatar' => null],
                                ['name' => 'Mike Williams', 'role' => 'Entrepreneur', 'content' => 'Great value and outstanding customer service. They truly care about their clients.', 'avatar' => null],
                            ],
                        ],
                    ],
                ],
            ],
            'portfolio' => [
                'slug' => 'portfolio',
                'title' => 'Our Work',
                'template' => 'portfolio',
                'metaDescription' => "View our portfolio of work at {$businessName}.",
                'content' => [
                    [
                        'type' => 'hero',
                        'props' => [
                            'heading' => 'Our Work',
                            'subheading' => 'Browse our portfolio of completed projects.',
                            'alignment' => 'center',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'props' => [
                            'content' => '<p style="text-align: center;">We take pride in every project we undertake. Here are some examples of our work.</p>',
                            'alignment' => 'center',
                        ],
                    ],
                    [
                        'type' => 'call-to-action',
                        'props' => [
                            'heading' => 'Want to See Your Project Here?',
                            'description' => 'Contact us to discuss your next project.',
                            'buttonText' => 'Start Your Project',
                            'buttonLink' => '/contact',
                            'backgroundColor' => 'primary',
                        ],
                    ],
                ],
            ],
        ];

        return Arr::get($defaults, $pageSlug, [
            'slug' => $pageSlug,
            'title' => ucfirst($pageSlug),
            'template' => 'default',
            'metaDescription' => "{$pageSlug} - {$businessName}",
            'content' => [
                [
                    'type' => 'hero',
                    'props' => [
                        'heading' => ucfirst($pageSlug),
                        'subheading' => 'Welcome to this page.',
                        'alignment' => 'center',
                    ],
                ],
                [
                    'type' => 'text',
                    'props' => [
                        'content' => '<p>Content for this page will be added soon.</p>',
                        'alignment' => 'left',
                    ],
                ],
            ],
        ]);
    }
}
