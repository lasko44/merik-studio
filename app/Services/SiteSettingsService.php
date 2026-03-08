<?php

namespace App\Services;

use App\Models\SiteSettings;
use Illuminate\Support\Arr;

/**
 * Handles site settings management and updates.
 */
class SiteSettingsService
{
    /**
     * Get the current site settings.
     */
    public function get(): SiteSettings
    {
        return SiteSettings::current();
    }

    /**
     * Update site settings with the provided data.
     */
    public function update(array $data): SiteSettings
    {
        $settings = SiteSettings::current();

        $updateData = $this->prepareUpdateData($data);

        $settings->update($updateData);

        return $settings->fresh();
    }

    /**
     * Apply a theme preset to the site settings.
     */
    public function applyTheme(string $themeName): bool
    {
        $settings = SiteSettings::current();

        return $settings->applyTheme($themeName);
    }

    /**
     * Prepare the data for update by filtering allowed fields.
     */
    protected function prepareUpdateData(array $data): array
    {
        $allowedFields = [
            // General
            'site_name',
            'tagline',
            'logo_path',
            'show_site_name_in_nav',
            'nav_logo_height',
            'favicon_path',

            // Theme
            'theme',
            'primary_color',
            'secondary_color',
            'accent_color',
            'background_color',
            'text_color',

            // Typography
            'heading_font',
            'body_font',

            // Contact
            'contact_email',
            'contact_phone',
            'contact_address',

            // Social
            'social_links',

            // SEO
            'default_meta_description',
            'default_meta_keywords',
            'og_default_image',
            'twitter_handle',
            'twitter_card_type',

            // Organization
            'organization_name',
            'organization_logo',
            'organization_type',
            'same_as',

            // AI/Robots
            'llms_txt_content',
            'llms_crawl_delay',
            'llms_allow_training',
            'robots_txt_content',

            // Features
            'sitemap_enabled',
            'geo_enabled',

            // Scripts
            'head_scripts',
            'body_scripts',

            // Stripe
            'stripe_publishable_key',
            'stripe_secret_key',
            'stripe_webhook_secret',
            'stripe_test_mode',
        ];

        $updateData = [];

        foreach ($allowedFields as $field) {
            if (Arr::has($data, $field)) {
                $updateData[$field] = Arr::get($data, $field);
            }
        }

        return $updateData;
    }

    /**
     * Get CSS variables for the current theme.
     */
    public function getCssVariables(): array
    {
        return $this->get()->getCssVariables();
    }

    /**
     * Get inline styles for theme injection.
     */
    public function getInlineStyles(): string
    {
        return $this->get()->getInlineStyles();
    }

    /**
     * Clear cached settings.
     */
    public function clearCache(): void
    {
        SiteSettings::clearCache();
    }

    /**
     * Get available organization types for schema.org selection.
     *
     * @return array<string, string>
     */
    public function getOrganizationTypes(): array
    {
        return [
            'Organization' => 'Organization (General)',
            'LocalBusiness' => 'Local Business',
            'Corporation' => 'Corporation',
            'EducationalOrganization' => 'Educational Organization',
            'GovernmentOrganization' => 'Government Organization',
            'MedicalOrganization' => 'Medical Organization',
            'NGO' => 'Non-Governmental Organization',
            'PerformingGroup' => 'Performing Group',
            'SportsOrganization' => 'Sports Organization',
        ];
    }
}
