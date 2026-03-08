<?php

/**
 * CMS Feature Flags and Configuration.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe Integration
    |--------------------------------------------------------------------------
    |
    | Enable or disable Stripe payment integration. When enabled, users can
    | configure Stripe API keys in the admin settings and use checkout
    | components in the page builder.
    |
    */
    'stripe_enabled' => env('CMS_STRIPE_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Invitation Expiry
    |--------------------------------------------------------------------------
    |
    | Number of days before a user invitation expires. After this period,
    | the invitation link will no longer work and a new invitation must
    | be sent.
    |
    */
    'invitation_expiry_days' => env('CMS_INVITATION_EXPIRY_DAYS', 7),
];
