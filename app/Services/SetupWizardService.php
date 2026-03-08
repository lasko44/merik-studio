<?php

namespace App\Services;

use App\Models\ContentEmbedding;
use App\Models\Page;
use App\Models\SiteSettings;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Orchestrates site generation from setup wizard data.
 */
class SetupWizardService
{
    public function __construct(
        protected AIContentService $aiContentService,
        protected ContentRAGService $ragService,
        protected PageService $pageService,
        protected SiteSettingsService $settingsService,
    ) {}

    /**
     * Generate a complete site from wizard data.
     */
    public function generate(User $user, array $wizardData): array
    {
        try {
            DB::beginTransaction();

            // Delete existing non-admin pages first
            $this->deleteExistingPages();

            // Determine if RAG is available (embeddings exist)
            $useRAG = $this->isRAGAvailable();

            Log::info('Setup wizard generation starting', [
                'use_rag' => $useRAG,
                'pages_requested' => Arr::get($wizardData, 'pages', []),
            ]);

            if ($useRAG) {
                // Use RAG-based generation for richer, context-aware content
                $generatedContent = $this->generateContentWithRAG($wizardData);
            } else {
                // Fallback to standard AI content generation
                $generatedContent = $this->aiContentService->generateSiteContent($wizardData);
            }

            // Update site settings
            $this->updateSettings($wizardData, $generatedContent);

            // Create pages with generated content
            $createdPages = $this->createPages($user, $wizardData, $generatedContent);

            // Set up navigation
            $this->updateNavigation($createdPages, $wizardData);

            DB::commit();

            return [
                'success' => true,
                'pages_created' => count($createdPages),
                'message' => 'Site generated successfully!',
                'used_rag' => $useRAG,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Setup wizard generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Check if RAG is available (embeddings have been seeded).
     */
    protected function isRAGAvailable(): bool
    {
        try {
            return ContentEmbedding::count() > 0;
        } catch (\Exception $e) {
            Log::warning('RAG availability check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Generate content using RAG-based approach.
     */
    protected function generateContentWithRAG(array $wizardData): array
    {
        $pages = Arr::get($wizardData, 'pages', ['home', 'about', 'contact']);
        $businessName = Arr::get($wizardData, 'businessName', 'Business');
        $description = Arr::get($wizardData, 'description', '');
        $industry = Arr::get($wizardData, 'industry', 'other');
        $primaryGoal = Arr::get($wizardData, 'primaryGoal', 'leads');

        // Clear any cached content to ensure fresh generation
        $this->ragService->clearCache($wizardData);

        Log::info('Generating content with RAG', [
            'pages' => $pages,
            'business' => $businessName,
        ]);

        // Get PageContentBuilder for fallback content
        $contentBuilder = app(PageContentBuilder::class);

        // Generate pages using RAG
        $generatedPages = [];
        foreach ($pages as $pageSlug) {
            $pageContent = $this->ragService->generatePageContent($pageSlug, $wizardData);

            // Get content from RAG, or fallback to PageContentBuilder if empty
            $content = Arr::get($pageContent, 'content', []);
            $template = Arr::get($pageContent, 'template');
            $metaDescription = Arr::get($pageContent, 'metaDescription');

            // If RAG returned empty content, use PageContentBuilder as fallback
            if (empty($content)) {
                Log::info("RAG returned empty content for '{$pageSlug}', using PageContentBuilder fallback");
                $fallbackContent = $contentBuilder->buildPageContent($pageSlug, $wizardData);
                $content = Arr::get($fallbackContent, 'content', []);
                $template = $template ?: Arr::get($fallbackContent, 'template', 'default');
                $metaDescription = $metaDescription ?: Arr::get($fallbackContent, 'metaDescription');
            }

            // Get recommended template if not set
            if (empty($template)) {
                $template = $this->aiContentService->getRecommendedTemplate($pageSlug, $industry, $primaryGoal);
            }

            // Generate proper page title
            $title = $this->getPageTitle($pageSlug, $businessName);

            $generatedPages[] = [
                'slug' => $pageSlug,
                'title' => $title,
                'template' => $template,
                'content' => $content,
                'metaDescription' => $metaDescription ?: "{$title} - {$businessName}",
            ];

            Log::debug("RAG generated page '{$pageSlug}'", [
                'blocks' => count($content),
                'template' => $template,
                'used_fallback' => empty(Arr::get($pageContent, 'content', [])),
            ]);
        }

        // Build navigation from pages with proper titles
        $navigation = [];
        $order = 0;
        foreach ($pages as $pageSlug) {
            if ($pageSlug !== 'home') {
                $navigation[] = [
                    'title' => $this->getPageTitle($pageSlug, $businessName),
                    'path' => '/' . $pageSlug,
                    'order' => $order,
                ];
            }
            $order++;
        }

        // Generate site-wide settings (tagline, meta) using quick AI call
        $tagline = $this->generateTagline($wizardData);

        return [
            'settings' => [
                'tagline' => $tagline,
                'metaDescription' => substr($description, 0, 160) ?: "Welcome to {$businessName}. Professional services tailored to your needs.",
            ],
            'pages' => $generatedPages,
            'navigation' => $navigation,
        ];
    }

    /**
     * Generate a tagline for the business.
     */
    protected function generateTagline(array $wizardData): string
    {
        $businessName = Arr::get($wizardData, 'businessName', 'Business');
        $description = Arr::get($wizardData, 'description', '');
        $industry = Arr::get($wizardData, 'industry', 'other');
        $tagline = Arr::get($wizardData, 'tagline', '');

        // If user provided a tagline, use it
        if (!empty($tagline)) {
            return $tagline;
        }

        // Generate based on description
        if (!empty($description)) {
            // Extract first sentence or create from description
            $firstSentence = strtok($description, '.');
            if (strlen($firstSentence) <= 60) {
                return $firstSentence;
            }
        }

        // Default taglines by industry
        $industryTaglines = [
            'restaurant' => 'Exceptional Dining, Unforgettable Experiences',
            'saas' => 'Powerful Solutions for Modern Business',
            'consultant' => 'Expert Guidance for Your Success',
            'agency' => 'Creative Solutions, Real Results',
            'ecommerce' => 'Quality Products, Exceptional Service',
            'portfolio' => 'Crafting Excellence, One Project at a Time',
            'healthcare' => 'Caring for You, Every Step of the Way',
            'realestate' => 'Your Dream Home Awaits',
            'fitness' => 'Transform Your Life, Achieve Your Goals',
            'education' => 'Empowering Minds, Shaping Futures',
        ];

        return Arr::get($industryTaglines, $industry, 'Professional Solutions for Your Needs');
    }

    /**
     * Save wizard data for persistence (used for manual saves outside of generation).
     */
    public function saveWizardData(array $wizardData): void
    {
        $settings = SiteSettings::current();
        $settings->update(['wizard_data' => $wizardData]);
        SiteSettings::clearCache();
    }

    /**
     * Get saved wizard data.
     */
    public function getSavedWizardData(): ?array
    {
        $settings = SiteSettings::current();
        return $settings->wizard_data;
    }

    /**
     * Delete all existing non-admin pages.
     */
    protected function deleteExistingPages(): void
    {
        // Delete all non-admin pages (force delete to completely remove them)
        Page::where('is_admin_page', false)->forceDelete();

        Log::info('Cleared existing pages for setup wizard regeneration');
    }

    /**
     * Update site settings based on wizard data.
     */
    protected function updateSettings(array $wizardData, array $generatedContent): void
    {
        $settings = SiteSettings::current();

        $updateData = [
            'site_name' => Arr::get($wizardData, 'businessName'),
            'tagline' => Arr::get($generatedContent, 'settings.tagline'),
            'default_meta_description' => Arr::get($generatedContent, 'settings.metaDescription'),
            'organization_name' => Arr::get($wizardData, 'businessName'),
            'wizard_data' => $wizardData, // Persist wizard data with settings
        ];

        // Apply theme based on style preference
        $style = Arr::get($wizardData, 'style', 'modern');
        $themeName = config("ai.style_themes.{$style}", 'modern');

        // Apply the theme first
        $settings->applyTheme($themeName);

        // Then override with color mood if specified
        $colorMood = Arr::get($wizardData, 'colorMood', 'professional');
        $colors = $this->aiContentService->generateColorPalette(
            $colorMood,
            Arr::get($wizardData, 'primaryColor')
        );

        $updateData['primary_color'] = Arr::get($colors, 'primary', $settings->primary_color);
        $updateData['secondary_color'] = Arr::get($colors, 'secondary', $settings->secondary_color);
        $updateData['accent_color'] = Arr::get($colors, 'accent', $settings->accent_color);

        // Handle logo if provided
        if ($logo = Arr::get($wizardData, 'logo')) {
            $updateData['logo_path'] = $logo;
        }

        // Contact info from wizard
        $updateData['contact_email'] = Arr::get($wizardData, 'contactEmail');
        $updateData['contact_phone'] = Arr::get($wizardData, 'contactPhone');
        $updateData['contact_address'] = Arr::get($wizardData, 'contactAddress');

        // Filter out null values but keep empty strings and arrays (including wizard_data)
        $filteredData = array_filter($updateData, fn ($value) => $value !== null);

        $settings->update($filteredData);
        SiteSettings::clearCache();

        Log::info('Site settings updated with wizard data', [
            'has_wizard_data' => isset($filteredData['wizard_data']),
            'wizard_data_keys' => array_keys($filteredData['wizard_data'] ?? []),
        ]);
    }

    /**
     * Create pages from wizard data and generated content.
     */
    protected function createPages(User $user, array $wizardData, array $generatedContent): array
    {
        $requestedPages = Arr::get($wizardData, 'pages', ['home']);
        $customPages = Arr::get($wizardData, 'customPages', []);
        $generatedPages = Arr::get($generatedContent, 'pages', []);

        Log::info('Creating pages from generated content', [
            'requested_pages' => $requestedPages,
            'generated_pages_count' => count($generatedPages),
            'generated_page_slugs' => array_column($generatedPages, 'slug'),
        ]);

        // Index generated pages by slug for easy lookup
        $generatedPagesBySlug = [];
        foreach ($generatedPages as $page) {
            $slug = Arr::get($page, 'slug', '');
            if ($slug) {
                $generatedPagesBySlug[$slug] = $page;
                Log::debug("Generated page '{$slug}' has " . count(Arr::get($page, 'content', [])) . ' content blocks');
            }
        }

        $createdPages = [];
        $navOrder = 0;

        foreach ($requestedPages as $pageSlug) {
            $generatedPage = Arr::get($generatedPagesBySlug, $pageSlug, []);

            Log::info("Building page data for '{$pageSlug}'", [
                'has_generated_content' => !empty($generatedPage),
                'content_blocks_from_ai' => count(Arr::get($generatedPage, 'content', [])),
            ]);

            $pageData = $this->buildPageData($pageSlug, $generatedPage, $wizardData, $navOrder);

            Log::info("Page data built for '{$pageSlug}'", [
                'content_blocks' => count(Arr::get($pageData, 'content', [])),
                'template' => Arr::get($pageData, 'template'),
            ]);

            $page = $this->pageService->create($user, $pageData);

            Log::info("Page created: '{$pageSlug}'", [
                'page_id' => $page->id,
                'saved_content_blocks' => count($page->content ?? []),
            ]);

            $createdPages[] = $page;

            $navOrder++;
        }

        // Create custom pages with better starter content
        $businessName = Arr::get($wizardData, 'businessName', 'Business');
        foreach ($customPages as $customPageName) {
            if (empty($customPageName)) {
                continue;
            }

            $slug = Page::generateSlug($customPageName);
            $pageData = [
                'title' => $customPageName,
                'slug' => $slug,
                'path' => '/' . $slug,
                'meta_description' => "{$customPageName} - {$businessName}",
                'twitter_card_type' => 'summary_large_image',
                'content' => [
                    [
                        'id' => uniqid('block_'),
                        'type' => 'hero',
                        'props' => [
                            'heading' => $customPageName,
                            'subheading' => "Learn more about {$customPageName} at {$businessName}.",
                            'primaryButtonText' => 'Contact Us',
                            'primaryButtonLink' => '/contact',
                            'alignment' => 'center',
                        ],
                    ],
                    [
                        'id' => uniqid('block_'),
                        'type' => 'text',
                        'props' => [
                            'content' => "<h2>About {$customPageName}</h2><p>Add your content here. Click to edit and customize this page with information about {$customPageName}.</p>",
                            'alignment' => 'left',
                        ],
                    ],
                    [
                        'id' => uniqid('block_'),
                        'type' => 'call-to-action',
                        'props' => [
                            'heading' => 'Have Questions?',
                            'description' => 'Get in touch with our team to learn more.',
                            'buttonText' => 'Contact Us',
                            'buttonLink' => '/contact',
                            'backgroundColor' => 'primary',
                        ],
                    ],
                ],
                'is_published' => true,
                'show_in_nav' => true,
                'nav_order' => $navOrder,
                'template' => 'default',
            ];

            $page = $this->pageService->create($user, $pageData);
            $createdPages[] = $page;
            $navOrder++;
        }

        return $createdPages;
    }

    /**
     * Build page data from generated content.
     */
    protected function buildPageData(string $pageSlug, array $generatedPage, array $wizardData, int $navOrder): array
    {
        $businessName = Arr::get($wizardData, 'businessName', 'Business');
        $industry = Arr::get($wizardData, 'industry', 'other');
        $primaryGoal = Arr::get($wizardData, 'primaryGoal', 'leads');

        // Get proper page title - prefer generated, fallback to standard titles
        $title = Arr::get($generatedPage, 'title') ?: $this->getPageTitle($pageSlug, $businessName);
        $content = Arr::get($generatedPage, 'content', []);
        $metaDescription = Arr::get($generatedPage, 'metaDescription', "{$title} - {$businessName}");

        // If content is empty, use PageContentBuilder as fallback
        if (empty($content)) {
            Log::info("Empty content for page '{$pageSlug}', using PageContentBuilder fallback");
            $contentBuilder = app(PageContentBuilder::class);
            $fallbackContent = $contentBuilder->buildPageContent($pageSlug, $wizardData);
            $content = Arr::get($fallbackContent, 'content', []);
        }

        // Get template from AI response or use recommended template
        $aiTemplate = Arr::get($generatedPage, 'template');
        $recommendedTemplate = $this->aiContentService->getRecommendedTemplate($pageSlug, $industry, $primaryGoal);

        // Use AI template if valid, otherwise use recommended template
        $validTemplates = ['default', 'full-width', 'sidebar', 'sidebar-left', 'landing', 'blog', 'portfolio', 'documentation', 'centered', 'product'];

        if (!empty($aiTemplate) && in_array($aiTemplate, $validTemplates)) {
            $template = $aiTemplate;
        } else {
            $template = $recommendedTemplate;
            Log::debug("Using recommended template for '{$pageSlug}'", [
                'ai_template' => $aiTemplate,
                'recommended' => $recommendedTemplate,
            ]);
        }

        // Final validation
        if (!in_array($template, $validTemplates)) {
            Log::warning("Invalid template '{$template}' for '{$pageSlug}', falling back to default");
            $template = 'default';
        }

        // Transform content blocks to ensure they have IDs and correct structure
        $transformedContent = $this->transformContentBlocks($content);

        // Handle sidebar content for sidebar templates
        $sidebarContent = [];
        if (in_array($template, ['sidebar', 'sidebar-left'])) {
            $sidebarContent = $this->transformContentBlocks(
                Arr::get($generatedPage, 'sidebarContent', $this->getDefaultSidebarContent($wizardData))
            );
        }

        $pageData = [
            'title' => $title,
            'slug' => $pageSlug,
            'path' => $pageSlug === 'home' ? '/' : '/' . $pageSlug,
            'meta_description' => $metaDescription,
            'twitter_card_type' => 'summary_large_image',
            'content' => $transformedContent,
            'is_published' => true,
            'show_in_nav' => $pageSlug !== 'home', // Home is typically not in nav (it's the logo link)
            'nav_order' => $navOrder,
            'template' => $template,
        ];

        // Add sidebar content if using sidebar template
        if (!empty($sidebarContent)) {
            $pageData['sidebar_content'] = $sidebarContent;
        }

        return $pageData;
    }

    /**
     * Get default sidebar content based on wizard data.
     */
    protected function getDefaultSidebarContent(array $wizardData): array
    {
        $businessName = Arr::get($wizardData, 'businessName', 'Business');
        $contactEmail = Arr::get($wizardData, 'contactEmail', '');
        $contactPhone = Arr::get($wizardData, 'contactPhone', '');
        $contactAddress = Arr::get($wizardData, 'contactAddress', '');

        return [
            [
                'type' => 'sidebar-cta',
                'props' => [
                    'title' => 'Need Help?',
                    'description' => 'Get in touch with our team today.',
                    'buttonText' => 'Contact Us',
                    'buttonLink' => '/contact',
                    'variant' => 'primary',
                ],
            ],
            [
                'type' => 'sidebar-contact',
                'props' => [
                    'title' => 'Contact Info',
                    'email' => $contactEmail,
                    'phone' => $contactPhone,
                    'address' => $contactAddress,
                    'showIcons' => true,
                ],
            ],
        ];
    }

    /**
     * Transform AI-generated content blocks to page builder format.
     */
    protected function transformContentBlocks(array $blocks): array
    {
        $transformed = [];

        foreach ($blocks as $block) {
            $type = Arr::get($block, 'type', 'text');
            $props = Arr::get($block, 'props', []);

            // Map any legacy/incorrect type names to correct component slugs
            $componentType = $this->mapBlockTypeToComponent($type);

            // Ensure proper prop structure for each component type
            $transformedProps = $this->ensureProperProps($componentType, $props);

            $transformed[] = [
                'id' => Arr::get($block, 'id', uniqid('block_')),
                'type' => $componentType,
                'props' => $transformedProps,
            ];
        }

        return $transformed;
    }

    /**
     * Map AI block type to page component slug.
     */
    protected function mapBlockTypeToComponent(string $type): string
    {
        $mapping = [
            // Direct matches (AI should use these)
            'hero' => 'hero',
            'features' => 'features',
            'text' => 'text',
            'call-to-action' => 'call-to-action',
            'contact-section' => 'contact-section',
            'testimonials' => 'testimonials',
            // Legacy/alternative names that might come from AI
            'cta' => 'call-to-action',
            'contact' => 'contact-section',
            'feature-grid' => 'features',
            'text-block' => 'text',
        ];

        return Arr::get($mapping, $type, $type);
    }

    /**
     * Ensure props have the correct structure for each component type.
     */
    protected function ensureProperProps(string $componentType, array $props): array
    {
        switch ($componentType) {
            case 'hero':
                return array_merge([
                    'heading' => 'Welcome',
                    'subheading' => '',
                    'primaryButtonText' => '',
                    'primaryButtonLink' => '',
                    'secondaryButtonText' => '',
                    'secondaryButtonLink' => '',
                    'backgroundImage' => null,
                    'overlayOpacity' => 50,
                    'alignment' => 'center',
                ], $this->mapLegacyHeroProps($props));

            case 'features':
                return array_merge([
                    'columns' => 3,
                    'features' => [],
                ], $props);

            case 'text':
                return array_merge([
                    'content' => '<p>Enter your content here...</p>',
                    'alignment' => 'left',
                ], $props);

            case 'call-to-action':
                return array_merge([
                    'heading' => '',
                    'description' => '',
                    'buttonText' => '',
                    'buttonLink' => '',
                    'backgroundColor' => 'primary',
                ], $this->mapLegacyCtaProps($props));

            case 'contact-section':
                return array_merge([
                    'title' => 'Contact Us',
                    'email' => '',
                    'phone' => '',
                    'address' => '',
                    'hours' => '',
                    'showForm' => true,
                ], $props);

            case 'testimonials':
                return array_merge([
                    'layout' => 'grid',
                    'testimonials' => [],
                ], $this->mapLegacyTestimonialProps($props));

            default:
                return $props;
        }
    }

    /**
     * Map legacy hero prop names to correct names.
     */
    protected function mapLegacyHeroProps(array $props): array
    {
        $mapped = $props;

        // Map legacy prop names if AI uses them
        if (isset($props['title']) && !isset($props['heading'])) {
            $mapped['heading'] = $props['title'];
            unset($mapped['title']);
        }
        if (isset($props['subtitle']) && !isset($props['subheading'])) {
            $mapped['subheading'] = $props['subtitle'];
            unset($mapped['subtitle']);
        }
        if (isset($props['buttonText']) && !isset($props['primaryButtonText'])) {
            $mapped['primaryButtonText'] = $props['buttonText'];
            unset($mapped['buttonText']);
        }
        if (isset($props['buttonLink']) && !isset($props['primaryButtonLink'])) {
            $mapped['primaryButtonLink'] = $props['buttonLink'];
            unset($mapped['buttonLink']);
        }

        return $mapped;
    }

    /**
     * Map legacy CTA prop names to correct names.
     */
    protected function mapLegacyCtaProps(array $props): array
    {
        $mapped = $props;

        // Map legacy prop names if AI uses them
        if (isset($props['title']) && !isset($props['heading'])) {
            $mapped['heading'] = $props['title'];
            unset($mapped['title']);
        }

        return $mapped;
    }

    /**
     * Map legacy testimonial prop names to correct names.
     */
    protected function mapLegacyTestimonialProps(array $props): array
    {
        $mapped = $props;

        // Ensure testimonials have correct structure
        if (isset($mapped['testimonials']) && is_array($mapped['testimonials'])) {
            $mapped['testimonials'] = array_map(function ($testimonial) {
                $result = $testimonial;
                // Map 'quote' to 'content' if used
                if (isset($testimonial['quote']) && !isset($testimonial['content'])) {
                    $result['content'] = $testimonial['quote'];
                    unset($result['quote']);
                }
                // Ensure avatar is set
                if (!isset($result['avatar'])) {
                    $result['avatar'] = null;
                }
                return $result;
            }, $mapped['testimonials']);
        }

        return $mapped;
    }

    /**
     * Update navigation based on created pages.
     */
    protected function updateNavigation(array $createdPages, array $wizardData): void
    {
        // Update nav_order for each page
        foreach ($createdPages as $index => $page) {
            // Skip home page in nav (it's typically accessed via logo)
            $showInNav = $page->path !== '/';

            $page->update([
                'show_in_nav' => $showInNav,
                'nav_order' => $index,
            ]);
        }
    }

    /**
     * Get wizard configuration for frontend.
     */
    public function getWizardConfig(): array
    {
        return [
            'industries' => config('ai.industries', []),
            'colorMoods' => array_keys(config('ai.color_moods', [])),
            'industryPresets' => config('ai.industry_presets', []),
        ];
    }

    /**
     * Get proper page title based on slug.
     */
    protected function getPageTitle(string $pageSlug, string $businessName): string
    {
        $titles = [
            'home' => 'Home',
            'about' => 'About Us',
            'services' => 'Our Services',
            'features' => 'Features',
            'contact' => 'Contact Us',
            'faq' => 'Frequently Asked Questions',
            'testimonials' => 'Testimonials',
            'pricing' => 'Pricing',
            'portfolio' => 'Our Work',
            'work' => 'Our Work',
            'gallery' => 'Gallery',
            'blog' => 'Blog',
            'team' => 'Our Team',
            'products' => 'Products',
        ];

        return Arr::get($titles, $pageSlug, ucfirst(str_replace('-', ' ', $pageSlug)));
    }
}
