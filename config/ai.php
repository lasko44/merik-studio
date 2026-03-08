<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for OpenAI API integration used by the setup wizard
    | and other AI-powered content generation features.
    |
    */

    'openai' => [
        'model' => env('OPENAI_MODEL', 'gpt-4o'),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 4000),
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
    ],

    /*
    |--------------------------------------------------------------------------
    | Color Mood Mappings
    |--------------------------------------------------------------------------
    |
    | Default color palettes for different mood selections in the setup wizard.
    |
    */

    'color_moods' => [
        'professional' => [
            'primary' => '#1e40af',
            'secondary' => '#374151',
            'accent' => '#0891b2',
        ],
        'friendly' => [
            'primary' => '#2563eb',
            'secondary' => '#7c3aed',
            'accent' => '#f59e0b',
        ],
        'energetic' => [
            'primary' => '#dc2626',
            'secondary' => '#ea580c',
            'accent' => '#fbbf24',
        ],
        'calm' => [
            'primary' => '#0d9488',
            'secondary' => '#64748b',
            'accent' => '#a3e635',
        ],
        'luxury' => [
            'primary' => '#1f2937',
            'secondary' => '#78350f',
            'accent' => '#d4af37',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Industry Presets
    |--------------------------------------------------------------------------
    |
    | Suggested pages and features for different industry selections.
    |
    */

    'industry_presets' => [
        'restaurant' => [
            'pages' => ['home', 'menu', 'about', 'contact', 'gallery'],
            'features' => ['contactForm', 'portfolio'],
        ],
        'consultant' => [
            'pages' => ['home', 'services', 'about', 'blog', 'contact'],
            'features' => ['blog', 'contactForm', 'testimonials'],
        ],
        'ecommerce' => [
            'pages' => ['home', 'products', 'about', 'contact', 'faq'],
            'features' => ['products', 'faq', 'newsletter'],
        ],
        'portfolio' => [
            'pages' => ['home', 'work', 'about', 'contact'],
            'features' => ['portfolio', 'contactForm'],
        ],
        'saas' => [
            'pages' => ['home', 'features', 'pricing', 'blog', 'contact'],
            'features' => ['blog', 'faq', 'newsletter'],
        ],
        'agency' => [
            'pages' => ['home', 'services', 'work', 'team', 'contact'],
            'features' => ['portfolio', 'team', 'testimonials'],
        ],
        'healthcare' => [
            'pages' => ['home', 'services', 'about', 'team', 'contact'],
            'features' => ['team', 'contactForm', 'faq'],
        ],
        'education' => [
            'pages' => ['home', 'courses', 'about', 'blog', 'contact'],
            'features' => ['blog', 'newsletter', 'faq'],
        ],
        'realestate' => [
            'pages' => ['home', 'listings', 'about', 'contact'],
            'features' => ['portfolio', 'contactForm', 'testimonials'],
        ],
        'other' => [
            'pages' => ['home', 'about', 'services', 'contact'],
            'features' => ['contactForm'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Available Industries
    |--------------------------------------------------------------------------
    |
    | List of industries for the setup wizard dropdown.
    |
    */

    'industries' => [
        'restaurant' => 'Restaurant / Food Service',
        'consultant' => 'Consulting / Professional Services',
        'ecommerce' => 'E-commerce / Retail',
        'portfolio' => 'Portfolio / Freelancer',
        'saas' => 'SaaS / Technology',
        'agency' => 'Agency / Creative',
        'healthcare' => 'Healthcare / Medical',
        'education' => 'Education / Training',
        'realestate' => 'Real Estate',
        'nonprofit' => 'Non-profit / Charity',
        'fitness' => 'Fitness / Wellness',
        'legal' => 'Legal Services',
        'finance' => 'Finance / Accounting',
        'construction' => 'Construction / Home Services',
        'other' => 'Other',
    ],

    /*
    |--------------------------------------------------------------------------
    | Style to Theme Mapping
    |--------------------------------------------------------------------------
    |
    | Maps wizard style preferences to existing theme presets.
    |
    */

    'style_themes' => [
        'modern' => 'modern',
        'classic' => 'classic',
        'minimal' => 'slate',
        'bold' => 'royal',
    ],
];
