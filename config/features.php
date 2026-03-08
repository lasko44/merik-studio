<?php

/**
 * Feature flags configuration for the CMS.
 *
 * These settings control which features are enabled for each customer site.
 * Only the developer (Super Admin) can modify these values in the code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Page Limit Tier
    |--------------------------------------------------------------------------
    |
    | Defines how many public pages the customer can create.
    | Options: 'basic' (10 pages), 'standard' (20 pages), 'unlimited'
    |
    */
    'page_limit_tier' => env('CMS_PAGE_LIMIT_TIER', 'basic'),

    /*
    |--------------------------------------------------------------------------
    | Calendar/Booking Feature
    |--------------------------------------------------------------------------
    |
    | Enable calendar functionality for booking appointments.
    |
    */
    'calendar_enabled' => env('CMS_CALENDAR_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Stripe/Cashier Integration
    |--------------------------------------------------------------------------
    |
    | Enable Laravel Cashier for Stripe payments and product management.
    |
    */
    'stripe_enabled' => env('CMS_STRIPE_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Blog Feature
    |--------------------------------------------------------------------------
    |
    | Enable blog functionality with posts and categories.
    |
    */
    'blog_enabled' => env('CMS_BLOG_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Email Campaigns
    |--------------------------------------------------------------------------
    |
    | Enable email campaign functionality for marketing.
    |
    */
    'email_campaigns_enabled' => env('CMS_EMAIL_CAMPAIGNS_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Super Admin Email
    |--------------------------------------------------------------------------
    |
    | The email address for the super admin (developer).
    |
    */
    'super_admin_email' => env('CMS_SUPER_ADMIN_EMAIL', 'matthew.laszkiewicz@gmail.com'),

];
