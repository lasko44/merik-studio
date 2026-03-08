<?php

/**
 * Page template definitions for the CMS.
 *
 * Templates define how page content is laid out and rendered on the public site.
 * Each template has configurable options that can be overridden per-page.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Template Definitions
    |--------------------------------------------------------------------------
    |
    | Each template has:
    | - name: Display name for the template
    | - description: Brief description of the template's purpose
    | - category: 'general', 'marketing', or 'content'
    | - sections: Array of section slugs this template supports
    | - options: Configurable options with their defaults
    | - preview: SVG preview identifier
    |
    */

    'default' => [
        'name' => 'Default',
        'description' => 'Standard contained layout with max-width constraints.',
        'category' => 'general',
        'sections' => ['content'],
        'section_definitions' => [
            'content' => [
                'label' => 'Main Content',
                'description' => 'Primary page content area',
                'required' => true,
                'defaultBranding' => ['paddingTop' => 'md', 'paddingBottom' => 'md', 'maxWidth' => 'max-w-7xl'],
                'defaultBlocks' => [
                    [
                        'type' => 'heading',
                        'data' => [
                            'text' => 'Page Title',
                            'level' => 'h1',
                            'alignment' => 'left',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'data' => [
                            'content' => '<p>Start adding your content here. You can add text, images, and other blocks to build your page.</p>',
                            'alignment' => 'left',
                        ],
                    ],
                ],
            ],
        ],
        'options' => [
            'container_width' => 'max-w-7xl',
        ],
        'preview' => 'default',
    ],

    'full-width' => [
        'name' => 'Full Width',
        'description' => 'Edge-to-edge layout ideal for hero sections and immersive content.',
        'category' => 'marketing',
        'sections' => ['content'],
        'section_definitions' => [
            'content' => [
                'label' => 'Main Content',
                'description' => 'Full-width content area',
                'required' => true,
                'defaultBranding' => ['paddingTop' => 'none', 'paddingBottom' => 'none', 'maxWidth' => 'full'],
                'defaultBlocks' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'heading' => 'Full Width Hero',
                            'subheading' => 'Create an immersive experience with edge-to-edge content',
                            'primaryButtonText' => 'Get Started',
                            'primaryButtonLink' => '#',
                            'secondaryButtonText' => '',
                            'secondaryButtonLink' => '',
                            'backgroundImage' => null,
                            'overlayOpacity' => 50,
                            'alignment' => 'center',
                        ],
                    ],
                ],
            ],
        ],
        'options' => [],
        'preview' => 'full-width',
    ],

    'sidebar' => [
        'name' => 'Sidebar Right',
        'description' => 'Two-column layout with sidebar on the right side.',
        'category' => 'general',
        'sections' => ['content', 'sidebar'],
        'section_definitions' => [
            'content' => [
                'label' => 'Main Content',
                'description' => 'Primary content area',
                'required' => true,
                'defaultBranding' => ['paddingTop' => 'md', 'paddingBottom' => 'md'],
                'defaultBlocks' => [
                    [
                        'type' => 'heading',
                        'data' => [
                            'text' => 'Page Title',
                            'level' => 'h1',
                            'alignment' => 'left',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'data' => [
                            'content' => '<p>Add your main content here. The sidebar on the right will display additional widgets and information.</p>',
                            'alignment' => 'left',
                        ],
                    ],
                ],
            ],
            'sidebar' => [
                'label' => 'Sidebar',
                'description' => 'Sidebar widgets area',
                'required' => false,
                'defaultBranding' => ['paddingTop' => 'md', 'paddingBottom' => 'md'],
                'defaultBlocks' => [
                    [
                        'type' => 'sidebar-card',
                        'data' => [
                            'title' => 'About',
                            'content' => '<p>Add sidebar content here.</p>',
                            'variant' => 'bordered',
                        ],
                    ],
                    [
                        'type' => 'sidebar-cta',
                        'data' => [
                            'title' => 'Need Help?',
                            'description' => 'Get in touch with us today.',
                            'buttonText' => 'Contact Us',
                            'buttonLink' => '/contact',
                            'variant' => 'primary',
                        ],
                    ],
                ],
            ],
        ],
        'options' => [
            'sidebar_width' => '1/3',
            'sidebar_sticky' => true,
        ],
        'preview' => 'sidebar',
    ],

    'sidebar-left' => [
        'name' => 'Sidebar Left',
        'description' => 'Two-column layout with sidebar on the left side.',
        'category' => 'general',
        'sections' => ['content', 'sidebar'],
        'section_definitions' => [
            'content' => [
                'label' => 'Main Content',
                'description' => 'Primary content area',
                'required' => true,
                'defaultBranding' => ['paddingTop' => 'md', 'paddingBottom' => 'md'],
                'defaultBlocks' => [
                    [
                        'type' => 'heading',
                        'data' => [
                            'text' => 'Page Title',
                            'level' => 'h1',
                            'alignment' => 'left',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'data' => [
                            'content' => '<p>Add your main content here. The sidebar on the left will display navigation or additional widgets.</p>',
                            'alignment' => 'left',
                        ],
                    ],
                ],
            ],
            'sidebar' => [
                'label' => 'Sidebar',
                'description' => 'Sidebar widgets area',
                'required' => false,
                'defaultBranding' => ['paddingTop' => 'md', 'paddingBottom' => 'md'],
                'defaultBlocks' => [
                    [
                        'type' => 'sidebar-links',
                        'data' => [
                            'title' => 'Quick Links',
                            'links' => [
                                ['text' => 'Home', 'url' => '/'],
                                ['text' => 'About', 'url' => '/about'],
                                ['text' => 'Contact', 'url' => '/contact'],
                            ],
                            'style' => 'list',
                        ],
                    ],
                    [
                        'type' => 'sidebar-contact',
                        'data' => [
                            'title' => 'Contact Us',
                            'email' => '',
                            'phone' => '',
                            'address' => '',
                            'showIcons' => true,
                        ],
                    ],
                ],
            ],
        ],
        'options' => [
            'sidebar_width' => '1/3',
            'sidebar_sticky' => true,
        ],
        'preview' => 'sidebar-left',
    ],

    'landing' => [
        'name' => 'Landing Page',
        'description' => 'Full-width hero with contained content sections below.',
        'category' => 'marketing',
        'sections' => ['hero', 'features', 'content', 'cta'],
        'section_definitions' => [
            'hero' => [
                'label' => 'Hero Section',
                'description' => 'Main banner at the top of the page',
                'required' => true,
                'defaultBranding' => ['paddingTop' => 'none', 'paddingBottom' => 'none', 'maxWidth' => 'full'],
                'defaultBlocks' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'heading' => 'Welcome to Our Site',
                            'subheading' => 'Discover amazing things with us',
                            'primaryButtonText' => 'Get Started',
                            'primaryButtonLink' => '#',
                            'secondaryButtonText' => 'Learn More',
                            'secondaryButtonLink' => '#',
                            'backgroundImage' => null,
                            'overlayOpacity' => 50,
                            'alignment' => 'center',
                        ],
                    ],
                ],
            ],
            'features' => [
                'label' => 'Features',
                'description' => 'Highlight key features or benefits',
                'required' => false,
                'defaultBranding' => ['paddingTop' => 'lg', 'paddingBottom' => 'lg', 'maxWidth' => 'max-w-6xl'],
                'defaultBlocks' => [
                    [
                        'type' => 'features',
                        'data' => [
                            'columns' => 3,
                            'features' => [
                                ['icon' => 'star', 'title' => 'Feature 1', 'description' => 'Description of your first amazing feature'],
                                ['icon' => 'heart', 'title' => 'Feature 2', 'description' => 'Description of your second amazing feature'],
                                ['icon' => 'check', 'title' => 'Feature 3', 'description' => 'Description of your third amazing feature'],
                            ],
                        ],
                    ],
                ],
            ],
            'content' => [
                'label' => 'Main Content',
                'description' => 'Primary content area',
                'required' => true,
                'defaultBranding' => ['paddingTop' => 'md', 'paddingBottom' => 'md', 'maxWidth' => 'max-w-6xl'],
                'defaultBlocks' => [
                    [
                        'type' => 'text',
                        'data' => [
                            'content' => '<h2>About Us</h2><p>Add your main content here. Tell your visitors about your business, products, or services.</p>',
                            'alignment' => 'left',
                        ],
                    ],
                ],
            ],
            'cta' => [
                'label' => 'Call to Action',
                'description' => 'Final call-to-action section',
                'required' => false,
                'defaultBranding' => ['backgroundColor' => '#f3f4f6', 'paddingTop' => 'lg', 'paddingBottom' => 'lg', 'maxWidth' => 'max-w-6xl'],
                'defaultBlocks' => [
                    [
                        'type' => 'call-to-action',
                        'data' => [
                            'heading' => 'Ready to get started?',
                            'description' => 'Join thousands of satisfied customers today.',
                            'buttonText' => 'Get Started',
                            'buttonLink' => '#',
                            'backgroundColor' => 'primary',
                        ],
                    ],
                ],
            ],
        ],
        'options' => [
            'hero_full_width' => true,
            'content_width' => 'max-w-6xl',
        ],
        'preview' => 'landing',
    ],

    'blog' => [
        'name' => 'Blog / Article',
        'description' => 'Narrow, reading-optimized layout for long-form content.',
        'category' => 'content',
        'sections' => ['content'],
        'section_definitions' => [
            'content' => [
                'label' => 'Article Content',
                'description' => 'Long-form article content',
                'required' => true,
                'defaultBranding' => ['paddingTop' => 'lg', 'paddingBottom' => 'lg', 'maxWidth' => 'max-w-3xl'],
                'defaultBlocks' => [
                    [
                        'type' => 'heading',
                        'data' => [
                            'text' => 'Article Title',
                            'level' => 'h1',
                            'alignment' => 'left',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'data' => [
                            'content' => '<p>Start writing your article here. This template is optimized for long-form reading with a narrow, focused layout.</p><p>Add paragraphs, headings, images, and other content to tell your story.</p>',
                            'alignment' => 'left',
                        ],
                    ],
                ],
            ],
        ],
        'options' => [
            'container_width' => 'max-w-3xl',
            'show_toc' => false,
        ],
        'preview' => 'blog',
    ],

    'portfolio' => [
        'name' => 'Portfolio',
        'description' => 'Grid-friendly showcase layout for visual content.',
        'category' => 'content',
        'sections' => ['header', 'gallery', 'content'],
        'section_definitions' => [
            'header' => [
                'label' => 'Header',
                'description' => 'Page header and introduction',
                'required' => false,
                'defaultBranding' => ['paddingTop' => 'lg', 'paddingBottom' => 'md', 'maxWidth' => 'max-w-6xl'],
                'defaultBlocks' => [
                    [
                        'type' => 'heading',
                        'data' => [
                            'text' => 'Our Portfolio',
                            'level' => 'h1',
                            'alignment' => 'center',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'data' => [
                            'content' => '<p style="text-align: center;">Browse our collection of work and projects.</p>',
                            'alignment' => 'center',
                        ],
                    ],
                ],
            ],
            'gallery' => [
                'label' => 'Gallery',
                'description' => 'Portfolio items grid',
                'required' => true,
                'defaultBranding' => ['paddingTop' => 'md', 'paddingBottom' => 'lg', 'maxWidth' => 'max-w-6xl'],
                'defaultBlocks' => [
                    [
                        'type' => 'portfolio-item',
                        'data' => [
                            'image' => null,
                            'title' => 'Project 1',
                            'category' => 'Category',
                            'description' => 'A brief description of this project.',
                            'link' => '',
                            'linkText' => 'View Project',
                            'aspectRatio' => 'square',
                            'showOverlay' => true,
                            'overlayStyle' => 'gradient',
                        ],
                    ],
                    [
                        'type' => 'portfolio-item',
                        'data' => [
                            'image' => null,
                            'title' => 'Project 2',
                            'category' => 'Category',
                            'description' => 'A brief description of this project.',
                            'link' => '',
                            'linkText' => 'View Project',
                            'aspectRatio' => 'square',
                            'showOverlay' => true,
                            'overlayStyle' => 'gradient',
                        ],
                    ],
                    [
                        'type' => 'portfolio-item',
                        'data' => [
                            'image' => null,
                            'title' => 'Project 3',
                            'category' => 'Category',
                            'description' => 'A brief description of this project.',
                            'link' => '',
                            'linkText' => 'View Project',
                            'aspectRatio' => 'square',
                            'showOverlay' => true,
                            'overlayStyle' => 'gradient',
                        ],
                    ],
                ],
            ],
            'content' => [
                'label' => 'Additional Content',
                'description' => 'Extra content below the gallery',
                'required' => false,
                'defaultBranding' => ['paddingTop' => 'md', 'paddingBottom' => 'lg', 'maxWidth' => 'max-w-6xl'],
                'defaultBlocks' => [
                    [
                        'type' => 'call-to-action',
                        'data' => [
                            'heading' => 'Want to work together?',
                            'description' => 'Get in touch to discuss your next project.',
                            'buttonText' => 'Contact Us',
                            'buttonLink' => '/contact',
                            'backgroundColor' => 'primary',
                        ],
                    ],
                ],
            ],
        ],
        'options' => [
            'container_width' => 'max-w-6xl',
            'grid_columns' => 3,
        ],
        'preview' => 'portfolio',
    ],

    'documentation' => [
        'name' => 'Documentation',
        'description' => 'Three-column layout with navigation and table of contents.',
        'category' => 'content',
        'sections' => ['navigation', 'content', 'toc'],
        'section_definitions' => [
            'navigation' => [
                'label' => 'Navigation',
                'description' => 'Documentation navigation menu',
                'required' => false,
                'defaultBranding' => ['paddingTop' => 'md', 'paddingBottom' => 'md'],
                'defaultBlocks' => [
                    [
                        'type' => 'sidebar-links',
                        'data' => [
                            'title' => 'Documentation',
                            'links' => [
                                ['text' => 'Getting Started', 'url' => '#'],
                                ['text' => 'Installation', 'url' => '#'],
                                ['text' => 'Configuration', 'url' => '#'],
                                ['text' => 'API Reference', 'url' => '#'],
                            ],
                            'style' => 'list',
                        ],
                    ],
                ],
            ],
            'content' => [
                'label' => 'Documentation Content',
                'description' => 'Main documentation content',
                'required' => true,
                'defaultBranding' => ['paddingTop' => 'md', 'paddingBottom' => 'lg', 'maxWidth' => 'max-w-4xl'],
                'defaultBlocks' => [
                    [
                        'type' => 'heading',
                        'data' => [
                            'text' => 'Documentation Title',
                            'level' => 'h1',
                            'alignment' => 'left',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'data' => [
                            'content' => '<p>Welcome to the documentation. Use the navigation on the left to browse different sections.</p><h2>Getting Started</h2><p>Add your documentation content here.</p>',
                            'alignment' => 'left',
                        ],
                    ],
                ],
            ],
            'toc' => [
                'label' => 'Table of Contents',
                'description' => 'On-page navigation',
                'required' => false,
                'defaultBranding' => ['paddingTop' => 'md', 'paddingBottom' => 'md'],
                'defaultBlocks' => [
                    [
                        'type' => 'sidebar-card',
                        'data' => [
                            'title' => 'On This Page',
                            'content' => '<ul><li><a href="#getting-started">Getting Started</a></li></ul>',
                            'variant' => 'default',
                        ],
                    ],
                ],
            ],
        ],
        'options' => [
            'show_nav' => true,
            'show_toc' => true,
            'nav_sticky' => true,
        ],
        'preview' => 'documentation',
    ],

    'centered' => [
        'name' => 'Centered',
        'description' => 'Small centered layout ideal for forms and focused content.',
        'category' => 'general',
        'sections' => ['content'],
        'section_definitions' => [
            'content' => [
                'label' => 'Centered Content',
                'description' => 'Focused content area',
                'required' => true,
                'defaultBranding' => ['paddingTop' => 'lg', 'paddingBottom' => 'lg', 'maxWidth' => 'max-w-3xl'],
                'defaultBlocks' => [
                    [
                        'type' => 'heading',
                        'data' => [
                            'text' => 'Page Title',
                            'level' => 'h1',
                            'alignment' => 'center',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'data' => [
                            'content' => '<p style="text-align: center;">This centered layout is perfect for focused content like forms, login pages, or simple announcements.</p>',
                            'alignment' => 'center',
                        ],
                    ],
                ],
            ],
        ],
        'options' => [
            'container_width' => 'max-w-2xl',
            'vertical_center' => false,
        ],
        'preview' => 'centered',
    ],

    'product' => [
        'name' => 'Product',
        'description' => 'Two-column layout optimized for product showcase with image gallery.',
        'category' => 'commerce',
        'sections' => ['gallery', 'details', 'specs', 'reviews'],
        'section_definitions' => [
            'gallery' => [
                'label' => 'Product Gallery',
                'description' => 'Product images and media',
                'required' => false,
                'defaultBranding' => ['paddingTop' => 'md', 'paddingBottom' => 'md', 'maxWidth' => 'max-w-6xl'],
                'defaultBlocks' => [
                    [
                        'type' => 'product-image-gallery',
                        'data' => [
                            'showThumbnails' => true,
                            'thumbnailPosition' => 'bottom',
                            'enableLightbox' => true,
                            'enableZoom' => true,
                        ],
                    ],
                ],
            ],
            'details' => [
                'label' => 'Product Details',
                'description' => 'Main product information',
                'required' => true,
                'defaultBranding' => ['paddingTop' => 'md', 'paddingBottom' => 'md', 'maxWidth' => 'max-w-6xl'],
                'defaultBlocks' => [
                    [
                        'type' => 'text',
                        'data' => [
                            'content' => '<h1>Product Name</h1><p>Add your product description here. Highlight the key features and benefits.</p>',
                            'alignment' => 'left',
                        ],
                    ],
                    [
                        'type' => 'add-to-cart-button',
                        'data' => [
                            'buttonText' => 'Add to Cart',
                            'buttonStyle' => 'primary',
                            'showQuantitySelector' => true,
                            'minQuantity' => 1,
                            'maxQuantity' => 10,
                            'showPrice' => true,
                            'showStock' => true,
                        ],
                    ],
                ],
            ],
            'specs' => [
                'label' => 'Specifications',
                'description' => 'Technical specifications',
                'required' => false,
                'defaultBranding' => ['paddingTop' => 'md', 'paddingBottom' => 'md', 'backgroundColor' => '#f9fafb', 'maxWidth' => 'max-w-6xl'],
                'defaultBlocks' => [
                    [
                        'type' => 'product-specifications',
                        'data' => [
                            'title' => 'Specifications',
                            'layout' => 'table',
                            'specifications' => [
                                ['label' => 'Material', 'value' => ''],
                                ['label' => 'Dimensions', 'value' => ''],
                                ['label' => 'Weight', 'value' => ''],
                            ],
                        ],
                    ],
                ],
            ],
            'reviews' => [
                'label' => 'Reviews',
                'description' => 'Customer reviews section',
                'required' => false,
                'defaultBranding' => ['paddingTop' => 'lg', 'paddingBottom' => 'lg', 'maxWidth' => 'max-w-6xl'],
                'defaultBlocks' => [
                    [
                        'type' => 'product-reviews',
                        'data' => [
                            'title' => 'Customer Reviews',
                            'allowSubmissions' => true,
                            'showRatingBreakdown' => true,
                            'reviewsPerPage' => 5,
                        ],
                    ],
                ],
            ],
        ],
        'options' => [
            'show_gallery' => true,
            'gallery_position' => 'left',
            'show_related' => false,
        ],
        'preview' => 'product',
    ],

    /*
    |--------------------------------------------------------------------------
    | Template Categories
    |--------------------------------------------------------------------------
    */

    'categories' => [
        'general' => [
            'label' => 'General',
            'description' => 'Standard layouts for most pages.',
        ],
        'marketing' => [
            'label' => 'Marketing',
            'description' => 'Layouts optimized for landing pages and promotions.',
        ],
        'content' => [
            'label' => 'Content',
            'description' => 'Layouts optimized for reading and showcasing content.',
        ],
        'commerce' => [
            'label' => 'Commerce',
            'description' => 'Layouts optimized for product pages and e-commerce.',
        ],
    ],
];
