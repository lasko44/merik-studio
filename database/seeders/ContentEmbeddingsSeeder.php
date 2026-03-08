<?php

namespace Database\Seeders;

use App\Models\ContentEmbedding;
use App\Services\EmbeddingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the content_embeddings table with component, template, and content examples
 * for RAG-based content generation.
 */
class ContentEmbeddingsSeeder extends Seeder
{
    public function run(): void
    {
        $embeddingService = app(EmbeddingService::class);

        // Clear existing embeddings
        ContentEmbedding::truncate();

        $this->seedComponents($embeddingService);
        $this->seedPageTemplates($embeddingService);
        $this->seedContentExamples($embeddingService);

        $this->command->info('Content embeddings seeded successfully.');
    }

    protected function seedComponents(EmbeddingService $embeddingService): void
    {
        $components = [
            [
                'name' => 'hero',
                'description' => 'Large hero section with heading, subheading, and call-to-action buttons. Use at the top of pages to make a strong first impression.',
                'example_content' => [
                    'id' => 'hero_example',
                    'type' => 'hero',
                    'props' => [
                        'heading' => 'Transform Your Business Today',
                        'subheading' => 'We provide innovative solutions that help businesses grow and succeed in the digital age.',
                        'primaryButtonText' => 'Get Started',
                        'primaryButtonLink' => '/contact',
                        'secondaryButtonText' => 'Learn More',
                        'secondaryButtonLink' => '/about',
                        'alignment' => 'center',
                    ],
                ],
            ],
            [
                'name' => 'features',
                'description' => 'Grid of features or services with icons, titles, and descriptions. Perfect for showcasing what you offer.',
                'example_content' => [
                    'id' => 'features_example',
                    'type' => 'features',
                    'props' => [
                        'columns' => 3,
                        'features' => [
                            ['icon' => 'star', 'title' => 'Quality Service', 'description' => 'We deliver exceptional results every time.'],
                            ['icon' => 'shield', 'title' => 'Trusted Partner', 'description' => 'Reliability you can count on.'],
                            ['icon' => 'zap', 'title' => 'Fast Delivery', 'description' => 'Quick turnaround without compromising quality.'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'text',
                'description' => 'Rich text content block for paragraphs, lists, and formatted content. Use for storytelling and detailed information.',
                'example_content' => [
                    'id' => 'text_example',
                    'type' => 'text',
                    'props' => [
                        'content' => '<h2>Our Story</h2><p>Founded with a passion for excellence, we have grown from a small team to an industry leader. Our journey has been defined by our commitment to our customers and our relentless pursuit of innovation.</p><p>Today, we continue to push boundaries and deliver solutions that make a real difference.</p>',
                        'alignment' => 'left',
                    ],
                ],
            ],
            [
                'name' => 'call-to-action',
                'description' => 'Prominent CTA section to drive conversions. Use at the end of pages to encourage user action.',
                'example_content' => [
                    'id' => 'cta_example',
                    'type' => 'call-to-action',
                    'props' => [
                        'heading' => 'Ready to Get Started?',
                        'description' => 'Contact us today for a free consultation and discover how we can help you achieve your goals.',
                        'buttonText' => 'Contact Us',
                        'buttonLink' => '/contact',
                        'backgroundColor' => 'primary',
                    ],
                ],
            ],
            [
                'name' => 'testimonials',
                'description' => 'Customer testimonials and reviews to build trust and social proof.',
                'example_content' => [
                    'id' => 'testimonials_example',
                    'type' => 'testimonials',
                    'props' => [
                        'layout' => 'grid',
                        'testimonials' => [
                            ['name' => 'John Smith', 'role' => 'CEO, Tech Corp', 'content' => 'Working with this team transformed our business. Highly recommended!', 'avatar' => null],
                            ['name' => 'Sarah Johnson', 'role' => 'Marketing Director', 'content' => 'Professional, responsive, and delivered exceptional results.', 'avatar' => null],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'contact-section',
                'description' => 'Contact information display with optional form. Use on contact pages.',
                'example_content' => [
                    'id' => 'contact_example',
                    'type' => 'contact-section',
                    'props' => [
                        'title' => 'Get in Touch',
                        'email' => 'hello@example.com',
                        'phone' => '(555) 123-4567',
                        'address' => '123 Business Street, City, State 12345',
                        'hours' => 'Monday - Friday: 9am - 5pm',
                        'showForm' => true,
                    ],
                ],
            ],
            [
                'name' => 'pricing',
                'description' => 'Pricing table showing different plans or packages.',
                'example_content' => [
                    'id' => 'pricing_example',
                    'type' => 'pricing',
                    'props' => [
                        'plans' => [
                            ['name' => 'Starter', 'price' => '$29', 'period' => '/month', 'description' => 'Perfect for individuals', 'features' => ['Basic support', 'Core features'], 'featured' => false],
                            ['name' => 'Professional', 'price' => '$79', 'period' => '/month', 'description' => 'For growing teams', 'features' => ['Priority support', 'Advanced features', 'Analytics'], 'featured' => true],
                            ['name' => 'Enterprise', 'price' => 'Custom', 'period' => '', 'description' => 'For large organizations', 'features' => ['Dedicated support', 'All features', 'Custom integrations'], 'featured' => false],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($components as $component) {
            $text = "Component: {$component['name']}. {$component['description']}";
            $embedding = $embeddingService->embed($text);

            ContentEmbedding::create([
                'type' => 'component',
                'name' => $component['name'],
                'description' => $component['description'],
                'example_content' => $component['example_content'],
                'embedding' => '[' . implode(',', $embedding) . ']',
            ]);

            $this->command->info("Seeded component: {$component['name']}");
        }
    }

    protected function seedPageTemplates(EmbeddingService $embeddingService): void
    {
        $templates = [
            [
                'name' => 'landing',
                'description' => 'Full-width landing page template with hero section, features, content blocks, and CTA. Best for home pages and marketing pages.',
                'example_content' => [
                    'template' => 'landing',
                    'components' => ['hero', 'features', 'text', 'testimonials', 'call-to-action'],
                    'layout' => 'full-width hero followed by contained content sections',
                ],
            ],
            [
                'name' => 'default',
                'description' => 'Standard page template with contained width. Good for about pages, general content, and text-heavy pages.',
                'example_content' => [
                    'template' => 'default',
                    'components' => ['hero', 'text', 'features', 'call-to-action'],
                    'layout' => 'contained width with comfortable reading experience',
                ],
            ],
            [
                'name' => 'centered',
                'description' => 'Narrow centered layout for focused content. Best for contact pages, forms, and simple pages.',
                'example_content' => [
                    'template' => 'centered',
                    'components' => ['hero', 'contact-section'],
                    'layout' => 'narrow centered content for focused experience',
                ],
            ],
            [
                'name' => 'full-width',
                'description' => 'Edge-to-edge content layout. Best for services pages, feature showcases, and visual pages.',
                'example_content' => [
                    'template' => 'full-width',
                    'components' => ['hero', 'features', 'text', 'call-to-action'],
                    'layout' => 'full width sections with impactful visuals',
                ],
            ],
            [
                'name' => 'sidebar',
                'description' => 'Two-column layout with sidebar. Best for blog pages, documentation, and content with navigation.',
                'example_content' => [
                    'template' => 'sidebar',
                    'components' => ['hero', 'text'],
                    'sidebarComponents' => ['sidebar-cta', 'sidebar-contact', 'sidebar-links'],
                    'layout' => 'main content with right sidebar for CTAs and navigation',
                ],
            ],
            [
                'name' => 'portfolio',
                'description' => 'Grid-based layout for showcasing work. Best for portfolio, gallery, and project pages.',
                'example_content' => [
                    'template' => 'portfolio',
                    'components' => ['hero', 'image-gallery', 'call-to-action'],
                    'layout' => 'grid showcase with visual focus',
                ],
            ],
        ];

        foreach ($templates as $template) {
            $text = "Page template: {$template['name']}. {$template['description']}";
            $embedding = $embeddingService->embed($text);

            ContentEmbedding::create([
                'type' => 'page_template',
                'name' => $template['name'],
                'description' => $template['description'],
                'example_content' => $template['example_content'],
                'embedding' => '[' . implode(',', $embedding) . ']',
            ]);

            $this->command->info("Seeded template: {$template['name']}");
        }
    }

    protected function seedContentExamples(EmbeddingService $embeddingService): void
    {
        $examples = [
            // Home page examples
            [
                'name' => 'home_consulting',
                'category' => 'home_page',
                'industry' => 'consultant',
                'description' => 'Home page for a consulting business with hero, services features, about text, testimonials, and CTA.',
                'example_content' => [
                    'template' => 'landing',
                    'content' => [
                        ['type' => 'hero', 'props' => ['heading' => 'Strategic Consulting for Business Growth', 'subheading' => 'We help businesses optimize operations, increase efficiency, and achieve sustainable growth.', 'primaryButtonText' => 'Schedule Consultation', 'primaryButtonLink' => '/contact', 'alignment' => 'center']],
                        ['type' => 'features', 'props' => ['columns' => 3, 'features' => [['icon' => 'target', 'title' => 'Strategic Planning', 'description' => 'Develop clear roadmaps for success.'], ['icon' => 'trending-up', 'title' => 'Growth Strategy', 'description' => 'Scale your business effectively.'], ['icon' => 'users', 'title' => 'Team Development', 'description' => 'Build high-performing teams.']]]],
                        ['type' => 'text', 'props' => ['content' => '<h2>Why Choose Our Consulting Services?</h2><p>With over 15 years of experience, we bring proven methodologies and fresh perspectives to every engagement. Our consultants have worked with Fortune 500 companies and startups alike.</p>', 'alignment' => 'left']],
                        ['type' => 'testimonials', 'props' => ['layout' => 'grid', 'testimonials' => [['name' => 'David Miller', 'role' => 'CEO, TechStart', 'content' => 'Their strategic insights helped us double our revenue in 18 months.', 'avatar' => null]]]],
                        ['type' => 'call-to-action', 'props' => ['heading' => 'Ready to Transform Your Business?', 'description' => 'Book a free strategy session today.', 'buttonText' => 'Get Free Consultation', 'buttonLink' => '/contact', 'backgroundColor' => 'primary']],
                    ],
                ],
            ],
            [
                'name' => 'home_saas',
                'category' => 'home_page',
                'industry' => 'saas',
                'description' => 'Home page for a SaaS product with hero, feature highlights, benefits, and CTA.',
                'example_content' => [
                    'template' => 'landing',
                    'content' => [
                        ['type' => 'hero', 'props' => ['heading' => 'Streamline Your Workflow', 'subheading' => 'The all-in-one platform that helps teams collaborate, track progress, and deliver results faster.', 'primaryButtonText' => 'Start Free Trial', 'primaryButtonLink' => '/signup', 'secondaryButtonText' => 'Watch Demo', 'secondaryButtonLink' => '/demo', 'alignment' => 'center']],
                        ['type' => 'features', 'props' => ['columns' => 3, 'features' => [['icon' => 'zap', 'title' => 'Lightning Fast', 'description' => 'Built for speed and performance.'], ['icon' => 'shield', 'title' => 'Enterprise Security', 'description' => 'Bank-level encryption and compliance.'], ['icon' => 'globe', 'title' => 'Works Everywhere', 'description' => 'Access from any device, anywhere.']]]],
                        ['type' => 'text', 'props' => ['content' => '<h2>Built for Modern Teams</h2><p>Whether you are a startup or enterprise, our platform scales with your needs. Join thousands of teams who have transformed how they work.</p><ul><li>Real-time collaboration</li><li>Automated workflows</li><li>Powerful integrations</li><li>Advanced analytics</li></ul>', 'alignment' => 'left']],
                        ['type' => 'call-to-action', 'props' => ['heading' => 'Start Your Free Trial Today', 'description' => 'No credit card required. Get started in minutes.', 'buttonText' => 'Try Free for 14 Days', 'buttonLink' => '/signup', 'backgroundColor' => 'primary']],
                    ],
                ],
            ],
            // About page examples
            [
                'name' => 'about_general',
                'category' => 'about_page',
                'industry' => null,
                'description' => 'About page with company story, mission/values, and team introduction.',
                'example_content' => [
                    'template' => 'default',
                    'content' => [
                        ['type' => 'hero', 'props' => ['heading' => 'About Our Company', 'subheading' => 'Learn about our journey, mission, and the team behind our success.', 'alignment' => 'center']],
                        ['type' => 'text', 'props' => ['content' => '<h2>Our Story</h2><p>Founded in 2015, we started with a simple mission: to make quality services accessible to everyone. What began as a small team with a big dream has grown into a company serving thousands of satisfied customers.</p><p>Our journey has been marked by continuous innovation, unwavering commitment to our customers, and a culture that values integrity above all.</p>', 'alignment' => 'left']],
                        ['type' => 'features', 'props' => ['columns' => 3, 'features' => [['icon' => 'target', 'title' => 'Our Mission', 'description' => 'To deliver exceptional value and transform how businesses operate.'], ['icon' => 'heart', 'title' => 'Our Values', 'description' => 'Integrity, innovation, and customer success drive everything we do.'], ['icon' => 'award', 'title' => 'Our Promise', 'description' => 'Quality results and dedicated support, every time.']]]],
                        ['type' => 'call-to-action', 'props' => ['heading' => 'Want to Learn More?', 'description' => 'Get in touch to discuss how we can help you.', 'buttonText' => 'Contact Us', 'buttonLink' => '/contact', 'backgroundColor' => 'primary']],
                    ],
                ],
            ],
            // Services page examples
            [
                'name' => 'services_professional',
                'category' => 'services_page',
                'industry' => null,
                'description' => 'Services page with detailed service descriptions and process explanation.',
                'example_content' => [
                    'template' => 'full-width',
                    'content' => [
                        ['type' => 'hero', 'props' => ['heading' => 'Our Services', 'subheading' => 'Comprehensive solutions tailored to your unique needs.', 'primaryButtonText' => 'Get a Quote', 'primaryButtonLink' => '/contact', 'alignment' => 'center']],
                        ['type' => 'features', 'props' => ['columns' => 3, 'features' => [['icon' => 'star', 'title' => 'Service One', 'description' => 'Detailed description of your first service and the value it provides.'], ['icon' => 'shield', 'title' => 'Service Two', 'description' => 'Detailed description of your second service and how it helps customers.'], ['icon' => 'zap', 'title' => 'Service Three', 'description' => 'Detailed description of your third service and its benefits.']]]],
                        ['type' => 'text', 'props' => ['content' => '<h2>Our Process</h2><p>We follow a proven methodology to ensure successful outcomes:</p><ol><li><strong>Discovery</strong> - We learn about your needs and goals</li><li><strong>Strategy</strong> - We develop a customized plan</li><li><strong>Execution</strong> - We implement with precision and care</li><li><strong>Review</strong> - We measure results and optimize</li></ol>', 'alignment' => 'left']],
                        ['type' => 'testimonials', 'props' => ['layout' => 'grid', 'testimonials' => [['name' => 'Client Name', 'role' => 'Position, Company', 'content' => 'Outstanding service and results. They exceeded our expectations.', 'avatar' => null]]]],
                        ['type' => 'call-to-action', 'props' => ['heading' => 'Ready to Get Started?', 'description' => 'Contact us for a free consultation.', 'buttonText' => 'Request a Quote', 'buttonLink' => '/contact', 'backgroundColor' => 'primary']],
                    ],
                ],
            ],
            // Contact page example
            [
                'name' => 'contact_standard',
                'category' => 'contact_page',
                'industry' => null,
                'description' => 'Contact page with simple hero and contact section.',
                'example_content' => [
                    'template' => 'centered',
                    'content' => [
                        ['type' => 'hero', 'props' => ['heading' => 'Get in Touch', 'subheading' => 'We would love to hear from you. Reach out and let us know how we can help.', 'alignment' => 'center']],
                        ['type' => 'contact-section', 'props' => ['title' => 'Contact Information', 'email' => 'hello@example.com', 'phone' => '(555) 123-4567', 'address' => '123 Main Street, City, State 12345', 'hours' => 'Monday - Friday: 9am - 5pm', 'showForm' => true]],
                    ],
                ],
            ],
            // FAQ page example
            [
                'name' => 'faq_standard',
                'category' => 'faq_page',
                'industry' => null,
                'description' => 'FAQ page with common questions and answers.',
                'example_content' => [
                    'template' => 'default',
                    'content' => [
                        ['type' => 'hero', 'props' => ['heading' => 'Frequently Asked Questions', 'subheading' => 'Find answers to common questions below.', 'alignment' => 'center']],
                        ['type' => 'text', 'props' => ['content' => '<h3>What services do you offer?</h3><p>We offer a comprehensive range of services tailored to your needs. Contact us to learn more about specific offerings.</p><h3>How can I get started?</h3><p>Simply reach out through our contact form or give us a call. We will schedule a consultation to discuss your needs.</p><h3>What is your pricing?</h3><p>Our pricing varies based on scope and requirements. We offer competitive rates and flexible packages to fit different budgets.</p><h3>Do you offer support?</h3><p>Yes! We provide dedicated support to all our clients. Our team is available to help you succeed.</p>', 'alignment' => 'left']],
                    ],
                ],
            ],
        ];

        foreach ($examples as $example) {
            $text = "Page example: {$example['name']}. Category: {$example['category']}. Industry: " . ($example['industry'] ?? 'general') . ". {$example['description']}";
            $embedding = $embeddingService->embed($text);

            ContentEmbedding::create([
                'type' => 'content_example',
                'name' => $example['name'],
                'category' => $example['category'],
                'industry' => $example['industry'],
                'description' => $example['description'],
                'example_content' => $example['example_content'],
                'embedding' => '[' . implode(',', $embedding) . ']',
            ]);

            $this->command->info("Seeded example: {$example['name']}");
        }
    }
}
