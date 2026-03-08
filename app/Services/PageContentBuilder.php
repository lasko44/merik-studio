<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

/**
 * Builds page content by selecting appropriate components and generating
 * content for each based on wizard data. Uses structured component selection
 * (like a human would) rather than relying on AI for structure.
 */
class PageContentBuilder
{
    /**
     * Page structure definitions - what components each page type should have.
     */
    protected array $pageStructures = [
        'home' => [
            'template' => 'landing',
            'components' => ['hero', 'features', 'text-why-us', 'testimonials', 'cta'],
        ],
        'about' => [
            'template' => 'default',
            'components' => ['hero', 'text-story', 'features-values', 'text-achievements', 'cta'],
        ],
        'services' => [
            'template' => 'full-width',
            'components' => ['hero', 'features-services', 'text-process', 'testimonials', 'cta'],
        ],
        'features' => [
            'template' => 'full-width',
            'components' => ['hero', 'features-detailed', 'text-benefits', 'cta'],
        ],
        'contact' => [
            'template' => 'centered',
            'components' => ['hero-simple', 'contact-section'],
        ],
        'faq' => [
            'template' => 'default',
            'components' => ['hero-simple', 'text-faq'],
        ],
        'testimonials' => [
            'template' => 'default',
            'components' => ['hero-simple', 'testimonials-full', 'cta'],
        ],
        'pricing' => [
            'template' => 'default',
            'components' => ['hero-simple', 'text-pricing-intro', 'pricing', 'cta'],
        ],
        'portfolio' => [
            'template' => 'portfolio',
            'components' => ['hero-simple', 'text-portfolio-intro', 'cta'],
        ],
        'blog' => [
            'template' => 'sidebar',
            'components' => ['hero-simple', 'text-blog-intro'],
        ],
        'team' => [
            'template' => 'default',
            'components' => ['hero-simple', 'text-team-intro', 'features-team', 'cta'],
        ],
    ];

    /**
     * Build content for a specific page based on wizard data.
     */
    public function buildPageContent(string $pageSlug, array $wizardData): array
    {
        $structure = $this->getPageStructure($pageSlug);
        $template = $structure['template'];
        $componentTypes = $structure['components'];

        $content = [];
        foreach ($componentTypes as $componentType) {
            $block = $this->buildComponent($componentType, $pageSlug, $wizardData);
            if ($block) {
                $content[] = $block;
            }
        }

        // Generate sidebar content for sidebar templates
        $sidebarContent = [];
        if (in_array($template, ['sidebar', 'sidebar-left'])) {
            $sidebarContent = $this->buildSidebarContent($wizardData);
        }

        return [
            'template' => $template,
            'content' => $content,
            'sidebarContent' => $sidebarContent,
            'metaDescription' => $this->generateMetaDescription($pageSlug, $wizardData),
        ];
    }

    /**
     * Get page structure or default.
     */
    protected function getPageStructure(string $pageSlug): array
    {
        return $this->pageStructures[$pageSlug] ?? [
            'template' => 'default',
            'components' => ['hero-simple', 'text-generic', 'cta'],
        ];
    }

    /**
     * Build a specific component with content.
     */
    protected function buildComponent(string $componentType, string $pageSlug, array $wizardData): ?array
    {
        $businessName = Arr::get($wizardData, 'businessName', 'Our Company');
        $description = Arr::get($wizardData, 'description', '');
        $services = array_filter(Arr::get($wizardData, 'services', []));
        $companyStory = Arr::get($wizardData, 'companyStory', '');
        $achievements = Arr::get($wizardData, 'achievements', '');
        $whyChooseUs = Arr::get($wizardData, 'whyChooseUs', '');
        $processSteps = Arr::get($wizardData, 'processSteps', '');
        $faqItems = Arr::get($wizardData, 'faqItems', '');
        $testimonialNames = Arr::get($wizardData, 'testimonialNames', '');
        $targetAudience = Arr::get($wizardData, 'targetAudience', '');
        $primaryGoal = Arr::get($wizardData, 'primaryGoal', 'leads');
        $contactEmail = Arr::get($wizardData, 'contactEmail', '');
        $contactPhone = Arr::get($wizardData, 'contactPhone', '');
        $contactAddress = Arr::get($wizardData, 'contactAddress', '');

        // Get CTA text based on goal
        $ctaText = $this->getCtaText($primaryGoal);
        $ctaLink = $primaryGoal === 'sales' ? '/products' : '/contact';

        switch ($componentType) {
            case 'hero':
                return [
                    'id' => uniqid('block_'),
                    'type' => 'hero',
                    'props' => [
                        'heading' => $this->generateHeading($pageSlug, $businessName, $description),
                        'subheading' => $this->generateSubheading($pageSlug, $description, $targetAudience),
                        'primaryButtonText' => $ctaText,
                        'primaryButtonLink' => $ctaLink,
                        'secondaryButtonText' => $pageSlug === 'home' ? 'Learn More' : '',
                        'secondaryButtonLink' => $pageSlug === 'home' ? '/about' : '',
                        'alignment' => 'center',
                    ],
                ];

            case 'hero-simple':
                return [
                    'id' => uniqid('block_'),
                    'type' => 'hero',
                    'props' => [
                        'heading' => $this->getPageTitle($pageSlug, $businessName),
                        'subheading' => $this->getPageSubtitle($pageSlug, $businessName),
                        'alignment' => 'center',
                    ],
                ];

            case 'features':
            case 'features-services':
                $features = $this->buildFeaturesFromServices($services, $businessName);
                return [
                    'id' => uniqid('block_'),
                    'type' => 'features',
                    'props' => [
                        'columns' => min(count($features), 3),
                        'features' => $features,
                    ],
                ];

            case 'features-values':
                return [
                    'id' => uniqid('block_'),
                    'type' => 'features',
                    'props' => [
                        'columns' => 3,
                        'features' => [
                            ['icon' => 'target', 'title' => 'Our Mission', 'description' => $this->extractMission($whyChooseUs, $description)],
                            ['icon' => 'heart', 'title' => 'Our Values', 'description' => 'Integrity, excellence, and dedication to our customers.'],
                            ['icon' => 'trending-up', 'title' => 'Our Vision', 'description' => "To be the trusted partner for {$targetAudience}."],
                        ],
                    ],
                ];

            case 'features-detailed':
                $features = $this->buildDetailedFeatures($services, $whyChooseUs);
                return [
                    'id' => uniqid('block_'),
                    'type' => 'features',
                    'props' => [
                        'columns' => min(count($features), 3),
                        'features' => $features,
                    ],
                ];

            case 'features-team':
                return [
                    'id' => uniqid('block_'),
                    'type' => 'features',
                    'props' => [
                        'columns' => 3,
                        'features' => [
                            ['icon' => 'users', 'title' => 'Experienced Team', 'description' => 'Our professionals bring years of industry experience.'],
                            ['icon' => 'award', 'title' => 'Certified Experts', 'description' => 'Qualified and trained to deliver excellence.'],
                            ['icon' => 'heart', 'title' => 'Customer Focused', 'description' => 'Your success is our priority.'],
                        ],
                    ],
                ];

            case 'text-why-us':
                $content = $this->buildWhyUsContent($businessName, $whyChooseUs, $achievements);
                return [
                    'id' => uniqid('block_'),
                    'type' => 'text',
                    'props' => [
                        'content' => $content,
                        'alignment' => 'left',
                    ],
                ];

            case 'text-story':
                $content = $this->buildStoryContent($businessName, $companyStory, $description);
                return [
                    'id' => uniqid('block_'),
                    'type' => 'text',
                    'props' => [
                        'content' => $content,
                        'alignment' => 'left',
                    ],
                ];

            case 'text-achievements':
                if (empty($achievements)) {
                    return null;
                }
                return [
                    'id' => uniqid('block_'),
                    'type' => 'text',
                    'props' => [
                        'content' => "<h2>Our Achievements</h2><p>{$achievements}</p>",
                        'alignment' => 'left',
                    ],
                ];

            case 'text-process':
                $content = $this->buildProcessContent($processSteps);
                return [
                    'id' => uniqid('block_'),
                    'type' => 'text',
                    'props' => [
                        'content' => $content,
                        'alignment' => 'left',
                    ],
                ];

            case 'text-faq':
                $content = $this->buildFaqContent($faqItems, $businessName, $services);
                return [
                    'id' => uniqid('block_'),
                    'type' => 'text',
                    'props' => [
                        'content' => $content,
                        'alignment' => 'left',
                    ],
                ];

            case 'text-benefits':
                return [
                    'id' => uniqid('block_'),
                    'type' => 'text',
                    'props' => [
                        'content' => $this->buildBenefitsContent($whyChooseUs, $services),
                        'alignment' => 'left',
                    ],
                ];

            case 'text-pricing-intro':
                return [
                    'id' => uniqid('block_'),
                    'type' => 'text',
                    'props' => [
                        'content' => "<h2>Choose the Right Plan for You</h2><p>We offer flexible pricing options to meet your needs. Contact us for custom solutions.</p>",
                        'alignment' => 'center',
                    ],
                ];

            case 'text-portfolio-intro':
                return [
                    'id' => uniqid('block_'),
                    'type' => 'text',
                    'props' => [
                        'content' => "<p style=\"text-align: center;\">Browse our portfolio of completed projects. Each project represents our commitment to quality and client satisfaction.</p>",
                        'alignment' => 'center',
                    ],
                ];

            case 'text-blog-intro':
                return [
                    'id' => uniqid('block_'),
                    'type' => 'text',
                    'props' => [
                        'content' => "<p>Stay updated with the latest insights, tips, and news from {$businessName}.</p>",
                        'alignment' => 'left',
                    ],
                ];

            case 'text-team-intro':
                return [
                    'id' => uniqid('block_'),
                    'type' => 'text',
                    'props' => [
                        'content' => "<h2>Meet Our Team</h2><p>Our talented team of professionals is dedicated to delivering exceptional results for every client.</p>",
                        'alignment' => 'center',
                    ],
                ];

            case 'text-generic':
                return [
                    'id' => uniqid('block_'),
                    'type' => 'text',
                    'props' => [
                        'content' => "<h2>" . ucfirst($pageSlug) . "</h2><p>{$description}</p>",
                        'alignment' => 'left',
                    ],
                ];

            case 'testimonials':
            case 'testimonials-full':
                $testimonials = $this->buildTestimonials($testimonialNames, $businessName, $services);
                return [
                    'id' => uniqid('block_'),
                    'type' => 'testimonials',
                    'props' => [
                        'layout' => $componentType === 'testimonials-full' ? 'grid' : 'grid',
                        'testimonials' => $testimonials,
                    ],
                ];

            case 'contact-section':
                return [
                    'id' => uniqid('block_'),
                    'type' => 'contact-section',
                    'props' => [
                        'title' => 'Get in Touch',
                        'email' => $contactEmail,
                        'phone' => $contactPhone,
                        'address' => $contactAddress,
                        'hours' => 'Monday - Friday: 9am - 5pm',
                        'showForm' => true,
                    ],
                ];

            case 'cta':
                return [
                    'id' => uniqid('block_'),
                    'type' => 'call-to-action',
                    'props' => [
                        'heading' => $this->getCtaHeading($primaryGoal, $pageSlug),
                        'description' => $this->getCtaDescription($primaryGoal, $businessName),
                        'buttonText' => $ctaText,
                        'buttonLink' => $ctaLink,
                        'backgroundColor' => 'primary',
                    ],
                ];

            case 'pricing':
                return [
                    'id' => uniqid('block_'),
                    'type' => 'pricing',
                    'props' => [
                        'plans' => [
                            [
                                'name' => 'Starter',
                                'price' => 'Contact Us',
                                'period' => '',
                                'description' => 'Perfect for small businesses',
                                'features' => ['Basic support', 'Core features', 'Email assistance'],
                                'featured' => false,
                            ],
                            [
                                'name' => 'Professional',
                                'price' => 'Contact Us',
                                'period' => '',
                                'description' => 'For growing businesses',
                                'features' => ['Priority support', 'Advanced features', 'Phone & email assistance', 'Custom solutions'],
                                'featured' => true,
                            ],
                            [
                                'name' => 'Enterprise',
                                'price' => 'Contact Us',
                                'period' => '',
                                'description' => 'For large organizations',
                                'features' => ['Dedicated support', 'All features', '24/7 assistance', 'Custom integrations', 'SLA guarantee'],
                                'featured' => false,
                            ],
                        ],
                    ],
                ];

            default:
                Log::warning("Unknown component type: {$componentType}");
                return null;
        }
    }

    /**
     * Build sidebar content.
     */
    protected function buildSidebarContent(array $wizardData): array
    {
        $businessName = Arr::get($wizardData, 'businessName', 'Our Company');
        $contactEmail = Arr::get($wizardData, 'contactEmail', '');
        $contactPhone = Arr::get($wizardData, 'contactPhone', '');
        $contactAddress = Arr::get($wizardData, 'contactAddress', '');

        return [
            [
                'id' => uniqid('sidebar_'),
                'type' => 'sidebar-cta',
                'props' => [
                    'title' => 'Need Help?',
                    'description' => 'Get in touch with our team.',
                    'buttonText' => 'Contact Us',
                    'buttonLink' => '/contact',
                    'variant' => 'primary',
                ],
            ],
            [
                'id' => uniqid('sidebar_'),
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
     * Generate heading based on page and business info.
     */
    protected function generateHeading(string $pageSlug, string $businessName, string $description): string
    {
        if ($pageSlug === 'home') {
            // Try to extract a compelling hook from description
            if (strlen($description) > 10) {
                $words = explode(' ', $description);
                if (count($words) > 3) {
                    return "Welcome to {$businessName}";
                }
            }
            return "Welcome to {$businessName}";
        }

        return $this->getPageTitle($pageSlug, $businessName);
    }

    /**
     * Generate subheading.
     */
    protected function generateSubheading(string $pageSlug, string $description, string $targetAudience): string
    {
        if (!empty($description)) {
            // Use first sentence or first 150 chars
            $firstSentence = strtok($description, '.!?');
            if (strlen($firstSentence) > 20 && strlen($firstSentence) < 200) {
                return $firstSentence . '.';
            }
            return substr($description, 0, 150) . (strlen($description) > 150 ? '...' : '');
        }

        if (!empty($targetAudience)) {
            return "Professional solutions for {$targetAudience}.";
        }

        return 'Professional solutions tailored to your needs.';
    }

    /**
     * Get page title.
     */
    protected function getPageTitle(string $pageSlug, string $businessName): string
    {
        $titles = [
            'home' => "Welcome to {$businessName}",
            'about' => "About {$businessName}",
            'services' => 'Our Services',
            'features' => 'Features',
            'contact' => 'Contact Us',
            'faq' => 'Frequently Asked Questions',
            'testimonials' => 'What Our Clients Say',
            'pricing' => 'Pricing',
            'portfolio' => 'Our Work',
            'blog' => 'Blog',
            'team' => 'Our Team',
        ];

        return $titles[$pageSlug] ?? ucfirst($pageSlug);
    }

    /**
     * Get page subtitle.
     */
    protected function getPageSubtitle(string $pageSlug, string $businessName): string
    {
        $subtitles = [
            'about' => 'Learn more about who we are and what we do.',
            'services' => 'Discover how we can help you succeed.',
            'features' => 'Explore what makes us different.',
            'contact' => "We'd love to hear from you.",
            'faq' => 'Find answers to common questions.',
            'testimonials' => 'Real feedback from real clients.',
            'pricing' => 'Transparent pricing for every need.',
            'portfolio' => 'Browse our recent projects.',
            'blog' => 'Insights, tips, and updates.',
            'team' => 'Meet the people behind our success.',
        ];

        return $subtitles[$pageSlug] ?? '';
    }

    /**
     * Get CTA text based on primary goal.
     */
    protected function getCtaText(string $primaryGoal): string
    {
        return match ($primaryGoal) {
            'leads' => 'Get a Free Quote',
            'sales' => 'Shop Now',
            'information' => 'Learn More',
            'portfolio' => 'View Our Work',
            'community' => 'Join Us',
            default => 'Contact Us',
        };
    }

    /**
     * Get CTA heading based on goal and page.
     */
    protected function getCtaHeading(string $primaryGoal, string $pageSlug): string
    {
        return match ($primaryGoal) {
            'leads' => 'Ready to Get Started?',
            'sales' => 'Start Shopping Today',
            'information' => 'Want to Learn More?',
            'portfolio' => 'Like What You See?',
            'community' => 'Join Our Community',
            default => 'Get in Touch',
        };
    }

    /**
     * Get CTA description.
     */
    protected function getCtaDescription(string $primaryGoal, string $businessName): string
    {
        return match ($primaryGoal) {
            'leads' => "Contact {$businessName} today for a free consultation.",
            'sales' => 'Browse our products and find exactly what you need.',
            'information' => "Discover more about what {$businessName} can offer.",
            'portfolio' => "Let's discuss your next project.",
            'community' => 'Become part of our growing community.',
            default => "Reach out to {$businessName} - we're here to help.",
        };
    }

    /**
     * Build features from services.
     */
    protected function buildFeaturesFromServices(array $services, string $businessName): array
    {
        $icons = ['star', 'shield', 'zap', 'check', 'target', 'award', 'heart', 'globe', 'users', 'trending-up'];
        $features = [];

        foreach ($services as $index => $service) {
            if (empty(trim($service))) {
                continue;
            }

            // Parse service - might be "Title - Description" format
            $parts = array_map('trim', explode(' - ', $service, 2));
            $title = $parts[0];
            $description = $parts[1] ?? "Professional {$title} services tailored to your needs.";

            $features[] = [
                'icon' => $icons[$index % count($icons)],
                'title' => $title,
                'description' => $description,
            ];
        }

        // Add defaults if no services provided
        if (empty($features)) {
            $features = [
                ['icon' => 'star', 'title' => 'Quality Service', 'description' => 'We deliver exceptional results every time.'],
                ['icon' => 'shield', 'title' => 'Reliable Partner', 'description' => 'Count on us for consistent, dependable service.'],
                ['icon' => 'zap', 'title' => 'Fast Delivery', 'description' => 'Quick turnaround without compromising quality.'],
            ];
        }

        return array_slice($features, 0, 6);
    }

    /**
     * Build detailed features.
     */
    protected function buildDetailedFeatures(array $services, string $whyChooseUs): array
    {
        $features = $this->buildFeaturesFromServices($services, '');

        // Enhance descriptions if whyChooseUs is provided
        if (!empty($whyChooseUs) && count($features) > 0) {
            $features[0]['description'] = substr($whyChooseUs, 0, 150) . (strlen($whyChooseUs) > 150 ? '...' : '');
        }

        return $features;
    }

    /**
     * Extract mission from whyChooseUs or description.
     */
    protected function extractMission(string $whyChooseUs, string $description): string
    {
        if (!empty($whyChooseUs)) {
            $firstSentence = strtok($whyChooseUs, '.!?');
            if (strlen($firstSentence) > 20) {
                return $firstSentence . '.';
            }
        }

        if (!empty($description)) {
            return substr($description, 0, 100) . (strlen($description) > 100 ? '...' : '');
        }

        return 'To deliver exceptional value and service to every customer.';
    }

    /**
     * Build why choose us content.
     */
    protected function buildWhyUsContent(string $businessName, string $whyChooseUs, string $achievements): string
    {
        $html = "<h2>Why Choose {$businessName}?</h2>";

        if (!empty($whyChooseUs)) {
            $html .= "<p>{$whyChooseUs}</p>";
        } else {
            $html .= "<p>We are committed to excellence and delivering results that exceed expectations. Our team of experienced professionals is dedicated to your success.</p>";
        }

        if (!empty($achievements)) {
            $html .= "<h3>Our Track Record</h3><p>{$achievements}</p>";
        }

        return $html;
    }

    /**
     * Build company story content.
     */
    protected function buildStoryContent(string $businessName, string $companyStory, string $description): string
    {
        $html = "<h2>Our Story</h2>";

        if (!empty($companyStory)) {
            $html .= "<p>{$companyStory}</p>";
        } elseif (!empty($description)) {
            $html .= "<p>{$description}</p>";
            $html .= "<p>We are dedicated to providing excellent service to our customers and building lasting relationships based on trust and results.</p>";
        } else {
            $html .= "<p>We started with a simple mission: to provide exceptional service and value to our customers. Today, we continue to uphold those values while growing and evolving to meet new challenges.</p>";
        }

        return $html;
    }

    /**
     * Build process content.
     */
    protected function buildProcessContent(string $processSteps): string
    {
        $html = "<h2>Our Process</h2>";

        if (!empty($processSteps)) {
            // Try to parse numbered steps
            $lines = preg_split('/\r?\n/', $processSteps);
            if (count($lines) > 1) {
                $html .= "<ol>";
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        // Remove leading numbers/bullets
                        $line = preg_replace('/^[\d\.\)\-\*]+\s*/', '', $line);
                        $html .= "<li><strong>" . $line . "</strong></li>";
                    }
                }
                $html .= "</ol>";
            } else {
                $html .= "<p>{$processSteps}</p>";
            }
        } else {
            $html .= "<ul>";
            $html .= "<li><strong>Consultation</strong> - We understand your needs and goals.</li>";
            $html .= "<li><strong>Planning</strong> - We create a customized solution.</li>";
            $html .= "<li><strong>Execution</strong> - We deliver with excellence and precision.</li>";
            $html .= "<li><strong>Support</strong> - We provide ongoing assistance and follow-up.</li>";
            $html .= "</ul>";
        }

        return $html;
    }

    /**
     * Build FAQ content.
     */
    protected function buildFaqContent(string $faqItems, string $businessName, array $services): string
    {
        $html = "";

        if (!empty($faqItems)) {
            // Try to parse Q&A format
            $lines = preg_split('/\r?\n/', $faqItems);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                // Check if it looks like a question
                if (str_contains($line, '?')) {
                    $html .= "<h3>{$line}</h3>";
                } else {
                    $html .= "<p>{$line}</p>";
                }
            }
        }

        // Add default FAQs if none provided
        if (empty($html)) {
            $serviceList = !empty($services) ? implode(', ', array_filter($services)) : 'our services';

            $html .= "<h3>What services do you offer?</h3>";
            $html .= "<p>We offer {$serviceList}. Contact us to learn more about how we can help you.</p>";

            $html .= "<h3>How can I get started?</h3>";
            $html .= "<p>Simply reach out through our contact form or give us a call. We'll schedule a consultation to discuss your needs.</p>";

            $html .= "<h3>What areas do you serve?</h3>";
            $html .= "<p>We serve clients locally and can accommodate remote work as needed. Contact us to discuss your specific situation.</p>";

            $html .= "<h3>Do you offer free consultations?</h3>";
            $html .= "<p>Yes! We offer free initial consultations to understand your needs and provide recommendations.</p>";
        }

        return $html;
    }

    /**
     * Build benefits content.
     */
    protected function buildBenefitsContent(string $whyChooseUs, array $services): string
    {
        $html = "<h2>Benefits</h2>";

        if (!empty($whyChooseUs)) {
            $html .= "<p>{$whyChooseUs}</p>";
        }

        $html .= "<ul>";
        $html .= "<li>Professional service from experienced experts</li>";
        $html .= "<li>Customized solutions for your specific needs</li>";
        $html .= "<li>Dedicated support throughout the process</li>";
        $html .= "<li>Proven results and satisfied customers</li>";
        $html .= "</ul>";

        return $html;
    }

    /**
     * Build testimonials from names or generate defaults.
     */
    protected function buildTestimonials(string $testimonialNames, string $businessName, array $services): array
    {
        $testimonials = [];
        $serviceStr = !empty($services) ? implode(', ', array_slice(array_filter($services), 0, 2)) : 'their services';

        if (!empty($testimonialNames)) {
            $names = preg_split('/[,\n]+/', $testimonialNames);
            $roles = ['Business Owner', 'CEO', 'Marketing Director', 'Operations Manager', 'Client', 'Customer'];
            $contents = [
                "Working with {$businessName} has been an excellent experience. They delivered exactly what we needed and exceeded our expectations.",
                "Professional, responsive, and results-driven. {$businessName} is our go-to partner for {$serviceStr}.",
                "I highly recommend {$businessName}. Their attention to detail and commitment to quality is unmatched.",
                "The team at {$businessName} truly understands our business needs. They've been instrumental in our success.",
            ];

            foreach (array_slice($names, 0, 4) as $index => $name) {
                $name = trim($name);
                if (empty($name)) continue;

                $testimonials[] = [
                    'name' => $name,
                    'role' => $roles[$index % count($roles)],
                    'content' => $contents[$index % count($contents)],
                    'avatar' => null,
                ];
            }
        }

        // Add defaults if needed
        if (count($testimonials) < 2) {
            $defaults = [
                ['name' => 'Sarah Johnson', 'role' => 'Business Owner', 'content' => "Working with {$businessName} has transformed our operations. Highly recommended!", 'avatar' => null],
                ['name' => 'Michael Chen', 'role' => 'Marketing Director', 'content' => "Professional, reliable, and excellent results. {$businessName} delivers every time.", 'avatar' => null],
                ['name' => 'Emily Williams', 'role' => 'CEO', 'content' => "The best decision we made was partnering with {$businessName}. Outstanding service!", 'avatar' => null],
            ];

            foreach ($defaults as $default) {
                if (count($testimonials) >= 3) break;
                $testimonials[] = $default;
            }
        }

        return $testimonials;
    }

    /**
     * Generate meta description for a page.
     */
    protected function generateMetaDescription(string $pageSlug, array $wizardData): string
    {
        $businessName = Arr::get($wizardData, 'businessName', 'Our Company');
        $description = Arr::get($wizardData, 'description', '');

        $meta = match ($pageSlug) {
            'home' => !empty($description) ? substr($description, 0, 155) : "{$businessName} - Professional solutions tailored to your needs.",
            'about' => "Learn about {$businessName} - our story, mission, and commitment to excellence.",
            'services' => "Explore professional services offered by {$businessName}. Quality solutions for your needs.",
            'features' => "Discover the features and capabilities that make {$businessName} the right choice.",
            'contact' => "Get in touch with {$businessName}. We're here to help with your questions and needs.",
            'faq' => "Find answers to frequently asked questions about {$businessName} and our services.",
            'testimonials' => "See what our clients say about {$businessName}. Real reviews from real customers.",
            'pricing' => "View pricing options from {$businessName}. Transparent pricing for every budget.",
            'portfolio' => "Browse the portfolio of {$businessName}. See our completed projects and work.",
            'blog' => "Read the latest insights and updates from {$businessName}.",
            'team' => "Meet the team at {$businessName}. Experienced professionals dedicated to your success.",
            default => "{$pageSlug} - {$businessName}",
        };

        return substr($meta, 0, 160);
    }
}
