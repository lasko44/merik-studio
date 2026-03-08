<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\SiteSettings;
use Illuminate\Database\Seeder;

/**
 * Seeds sample site content for demonstration purposes.
 */
class SampleSiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Configure site settings
        $settings = SiteSettings::first() ?? SiteSettings::create([]);
        $settings->update([
            'site_name' => 'Acme Digital Solutions',
            'tagline' => 'Transforming Ideas Into Digital Reality',
            'theme' => 'modern',
            'primary_color' => '#6366F1',
            'secondary_color' => '#EC4899',
            'accent_color' => '#14B8A6',
            'background_color' => '#F9FAFB',
            'text_color' => '#111827',
            'contact_email' => 'hello@acmedigital.com',
            'contact_phone' => '(555) 123-4567',
            'contact_address' => '123 Innovation Drive, Tech City, CA 94000',
            'default_meta_description' => 'Acme Digital Solutions - Your partner in digital transformation. Web development, design, and marketing solutions for growing businesses.',
            'default_meta_keywords' => 'web development, digital marketing, web design, SEO, business solutions',
            'social_links' => [
                ['platform' => 'twitter', 'url' => 'https://twitter.com/acmedigital'],
                ['platform' => 'linkedin', 'url' => 'https://linkedin.com/company/acmedigital'],
                ['platform' => 'facebook', 'url' => 'https://facebook.com/acmedigital'],
            ],
            'organization_name' => 'Acme Digital Solutions',
            'organization_type' => 'Corporation',
            'twitter_handle' => '@acmedigital',
            'sitemap_enabled' => true,
            'geo_enabled' => true,
            'llms_allow_training' => true,
        ]);

        // Create Homepage
        Page::updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'path' => '/',
                'meta_description' => 'Welcome to Acme Digital Solutions. We help businesses grow with cutting-edge web development and digital marketing strategies.',
                'meta_keywords' => 'digital agency, web development, marketing',
                'template' => 'default',
                'is_published' => true,
                'is_admin_page' => false,
                'sort_order' => 1,
                'schema_type' => 'WebPage',
                'priority' => 1.0,
                'change_frequency' => 'weekly',
                'content' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'headline' => 'Transform Your Business With Digital Excellence',
                            'subheadline' => 'We build beautiful, high-performing websites and digital experiences that drive results.',
                            'cta_text' => 'Get Started',
                            'cta_link' => '/contact',
                            'background_image' => null,
                        ],
                    ],
                    [
                        'type' => 'features',
                        'data' => [
                            'title' => 'Our Services',
                            'subtitle' => 'Everything you need to succeed online',
                            'features' => [
                                [
                                    'icon' => 'code',
                                    'title' => 'Web Development',
                                    'description' => 'Custom websites built with modern technologies for speed and scalability.',
                                ],
                                [
                                    'icon' => 'palette',
                                    'title' => 'UI/UX Design',
                                    'description' => 'Beautiful, intuitive designs that engage users and drive conversions.',
                                ],
                                [
                                    'icon' => 'chart',
                                    'title' => 'Digital Marketing',
                                    'description' => 'Data-driven strategies to grow your online presence and reach.',
                                ],
                                [
                                    'icon' => 'search',
                                    'title' => 'SEO Optimization',
                                    'description' => 'Rank higher in search results and attract more organic traffic.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'data' => [
                            'content' => '<h2>Why Choose Acme Digital?</h2><p>With over 10 years of experience, we\'ve helped hundreds of businesses establish their digital presence. Our team of experts combines creativity with technical excellence to deliver solutions that exceed expectations.</p><p>From startups to enterprise clients, we tailor our approach to meet your unique needs and goals.</p>',
                        ],
                    ],
                    [
                        'type' => 'cta',
                        'data' => [
                            'headline' => 'Ready to Start Your Project?',
                            'description' => 'Let\'s discuss how we can help your business grow.',
                            'button_text' => 'Contact Us Today',
                            'button_link' => '/contact',
                        ],
                    ],
                ],
            ]
        );

        // Create About Page
        Page::updateOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About Us',
                'path' => '/about',
                'meta_description' => 'Learn about Acme Digital Solutions - our mission, values, and the team behind our success.',
                'meta_keywords' => 'about us, team, mission, digital agency',
                'template' => 'default',
                'is_published' => true,
                'is_admin_page' => false,
                'sort_order' => 2,
                'schema_type' => 'AboutPage',
                'priority' => 0.8,
                'change_frequency' => 'monthly',
                'content' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'headline' => 'About Acme Digital',
                            'subheadline' => 'Passionate about helping businesses succeed in the digital world.',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'data' => [
                            'content' => '<h2>Our Story</h2><p>Founded in 2014, Acme Digital Solutions started with a simple mission: make professional web development accessible to businesses of all sizes.</p><p>What began as a two-person team has grown into a full-service digital agency with experts in design, development, marketing, and strategy. But our core values remain the same—deliver exceptional work, build lasting relationships, and help our clients thrive.</p><h2>Our Mission</h2><p>We believe every business deserves a powerful online presence. Our mission is to democratize digital excellence by providing world-class solutions at accessible prices.</p>',
                        ],
                    ],
                    [
                        'type' => 'features',
                        'data' => [
                            'title' => 'Our Values',
                            'features' => [
                                [
                                    'icon' => 'star',
                                    'title' => 'Excellence',
                                    'description' => 'We never settle for "good enough." Every project gets our best effort.',
                                ],
                                [
                                    'icon' => 'users',
                                    'title' => 'Partnership',
                                    'description' => 'We see ourselves as an extension of your team, not just a vendor.',
                                ],
                                [
                                    'icon' => 'lightbulb',
                                    'title' => 'Innovation',
                                    'description' => 'We stay ahead of trends to give you cutting-edge solutions.',
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        // Create Services Page
        Page::updateOrCreate(
            ['slug' => 'services'],
            [
                'title' => 'Services',
                'path' => '/services',
                'meta_description' => 'Explore our full range of digital services including web development, design, SEO, and digital marketing.',
                'meta_keywords' => 'services, web development, design, SEO, marketing',
                'template' => 'default',
                'is_published' => true,
                'is_admin_page' => false,
                'sort_order' => 3,
                'schema_type' => 'Service',
                'priority' => 0.9,
                'change_frequency' => 'monthly',
                'faqs' => [
                    [
                        'question' => 'How long does a typical website project take?',
                        'answer' => 'Most website projects take 4-8 weeks depending on complexity. Simple sites can be completed faster, while large e-commerce or custom applications may take longer.',
                    ],
                    [
                        'question' => 'Do you offer ongoing maintenance?',
                        'answer' => 'Yes! We offer monthly maintenance packages that include security updates, backups, content changes, and technical support.',
                    ],
                    [
                        'question' => 'What is your pricing structure?',
                        'answer' => 'We offer both fixed-price projects and hourly rates depending on the scope. Contact us for a free quote tailored to your needs.',
                    ],
                ],
                'content' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'headline' => 'Our Services',
                            'subheadline' => 'Comprehensive digital solutions to grow your business.',
                        ],
                    ],
                    [
                        'type' => 'features',
                        'data' => [
                            'title' => 'What We Offer',
                            'features' => [
                                [
                                    'icon' => 'code',
                                    'title' => 'Web Development',
                                    'description' => 'Custom websites, web applications, and e-commerce solutions built with modern frameworks like Laravel and Vue.js.',
                                ],
                                [
                                    'icon' => 'palette',
                                    'title' => 'UI/UX Design',
                                    'description' => 'User-centered design that looks great and converts visitors into customers.',
                                ],
                                [
                                    'icon' => 'search',
                                    'title' => 'SEO & Content',
                                    'description' => 'Search engine optimization and content strategy to improve your visibility.',
                                ],
                                [
                                    'icon' => 'megaphone',
                                    'title' => 'Digital Marketing',
                                    'description' => 'PPC, social media, and email marketing campaigns that deliver ROI.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'data' => [
                            'content' => '<h2>Our Process</h2><p><strong>1. Discovery</strong> - We learn about your business, goals, and target audience.</p><p><strong>2. Strategy</strong> - We develop a customized plan to achieve your objectives.</p><p><strong>3. Design</strong> - We create mockups and prototypes for your approval.</p><p><strong>4. Development</strong> - We build your solution with clean, maintainable code.</p><p><strong>5. Launch</strong> - We deploy and ensure everything runs smoothly.</p><p><strong>6. Support</strong> - We provide ongoing maintenance and optimization.</p>',
                        ],
                    ],
                ],
            ]
        );

        // Create Contact Page
        Page::updateOrCreate(
            ['slug' => 'contact'],
            [
                'title' => 'Contact Us',
                'path' => '/contact',
                'meta_description' => 'Get in touch with Acme Digital Solutions. We\'d love to hear about your project.',
                'meta_keywords' => 'contact, get in touch, quote, consultation',
                'template' => 'default',
                'is_published' => true,
                'is_admin_page' => false,
                'sort_order' => 4,
                'schema_type' => 'ContactPage',
                'priority' => 0.8,
                'change_frequency' => 'monthly',
                'content' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'headline' => 'Get In Touch',
                            'subheadline' => 'Have a project in mind? Let\'s talk about how we can help.',
                        ],
                    ],
                    [
                        'type' => 'contact_info',
                        'data' => [
                            'email' => 'hello@acmedigital.com',
                            'phone' => '(555) 123-4567',
                            'address' => '123 Innovation Drive, Tech City, CA 94000',
                            'hours' => 'Monday - Friday: 9am - 6pm PST',
                        ],
                    ],
                    [
                        'type' => 'contact_form',
                        'data' => [
                            'title' => 'Send Us a Message',
                            'fields' => ['name', 'email', 'phone', 'message'],
                            'submit_text' => 'Send Message',
                        ],
                    ],
                ],
            ]
        );

        // Create Blog Post
        Page::updateOrCreate(
            ['slug' => '10-web-design-trends-2026'],
            [
                'title' => '10 Web Design Trends for 2026',
                'path' => '/blog/10-web-design-trends-2026',
                'meta_description' => 'Discover the top web design trends shaping the digital landscape in 2026, from AI-powered interfaces to immersive experiences.',
                'meta_keywords' => 'web design, trends, 2026, UI design, UX',
                'og_title' => '10 Web Design Trends You Need to Know in 2026',
                'og_description' => 'Stay ahead of the curve with these emerging web design trends.',
                'template' => 'default',
                'is_published' => true,
                'is_admin_page' => false,
                'sort_order' => 5,
                'schema_type' => 'Article',
                'priority' => 0.6,
                'change_frequency' => 'yearly',
                'content' => [
                    [
                        'type' => 'text',
                        'data' => [
                            'content' => '<h1>10 Web Design Trends for 2026</h1><p class="lead">The web design landscape is constantly evolving. Here are the trends we\'re most excited about this year.</p><h2>1. AI-Powered Personalization</h2><p>Websites are getting smarter. AI now enables real-time personalization based on user behavior, preferences, and context.</p><h2>2. Immersive 3D Experiences</h2><p>With WebGL and WebXR maturing, expect more websites to incorporate 3D elements and even VR/AR experiences.</p><h2>3. Micro-Interactions</h2><p>Small animations and feedback loops make interfaces feel more alive and responsive.</p><h2>4. Dark Mode as Default</h2><p>More sites are embracing dark themes, both for aesthetics and reduced eye strain.</p><h2>5. Variable Fonts</h2><p>Single font files that can adjust weight, width, and other properties dynamically.</p><p><em>Continue reading for trends 6-10...</em></p>',
                        ],
                    ],
                ],
            ]
        );

        // Create Pricing Page
        Page::updateOrCreate(
            ['slug' => 'pricing'],
            [
                'title' => 'Pricing',
                'path' => '/pricing',
                'meta_description' => 'Transparent pricing for our web development and digital marketing services. Find the perfect plan for your business.',
                'meta_keywords' => 'pricing, plans, cost, packages',
                'template' => 'default',
                'is_published' => true,
                'is_admin_page' => false,
                'sort_order' => 6,
                'schema_type' => 'WebPage',
                'priority' => 0.7,
                'change_frequency' => 'monthly',
                'faqs' => [
                    [
                        'question' => 'Can I upgrade my plan later?',
                        'answer' => 'Absolutely! You can upgrade anytime and we\'ll prorate the difference.',
                    ],
                    [
                        'question' => 'Do you offer refunds?',
                        'answer' => 'We offer a 30-day satisfaction guarantee on all our services.',
                    ],
                ],
                'content' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'headline' => 'Simple, Transparent Pricing',
                            'subheadline' => 'Choose the plan that fits your needs.',
                        ],
                    ],
                    [
                        'type' => 'pricing',
                        'data' => [
                            'plans' => [
                                [
                                    'name' => 'Starter',
                                    'price' => '$2,500',
                                    'period' => 'one-time',
                                    'description' => 'Perfect for small businesses getting started online.',
                                    'features' => [
                                        '5-page website',
                                        'Mobile responsive',
                                        'Basic SEO setup',
                                        'Contact form',
                                        '30 days support',
                                    ],
                                ],
                                [
                                    'name' => 'Professional',
                                    'price' => '$5,000',
                                    'period' => 'one-time',
                                    'description' => 'For growing businesses that need more features.',
                                    'features' => [
                                        '10-page website',
                                        'Custom design',
                                        'Advanced SEO',
                                        'CMS integration',
                                        'Analytics setup',
                                        '90 days support',
                                    ],
                                    'featured' => true,
                                ],
                                [
                                    'name' => 'Enterprise',
                                    'price' => 'Custom',
                                    'period' => '',
                                    'description' => 'Full-scale solutions for large organizations.',
                                    'features' => [
                                        'Unlimited pages',
                                        'Custom development',
                                        'API integrations',
                                        'Dedicated support',
                                        'SLA guarantee',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $this->command->info('Sample site created successfully!');
        $this->command->info('- Site: Acme Digital Solutions');
        $this->command->info('- Pages: Home, About, Services, Contact, Blog, Pricing');
    }
}
