<?php

namespace App\Http\Requests\Settings;

use App\Models\SiteSettings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates data for updating site settings.
 */
class UpdateSiteSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('settings.update');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // General
            'site_name' => ['sometimes', 'required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:500'],
            'logo_path' => ['nullable', 'string', 'max:500'],
            'show_site_name_in_nav' => ['nullable', 'boolean'],
            'nav_logo_height' => ['nullable', 'integer', 'min:16', 'max:120'],
            'favicon_path' => ['nullable', 'string', 'max:500'],

            // Theme
            'theme' => ['sometimes', 'string', Rule::in(array_keys(SiteSettings::THEMES))],
            'primary_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'background_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'text_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'success_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'warning_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'error_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'info_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'surface_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'border_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'muted_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],

            // Typography
            'heading_font' => ['nullable', 'string', Rule::in(array_keys(SiteSettings::FONTS))],
            'body_font' => ['nullable', 'string', Rule::in(array_keys(SiteSettings::FONTS))],

            // Contact
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_address' => ['nullable', 'string', 'max:500'],

            // Social Links
            'social_links' => ['nullable', 'array'],
            'social_links.facebook' => ['nullable', 'url', 'max:500'],
            'social_links.twitter' => ['nullable', 'url', 'max:500'],
            'social_links.instagram' => ['nullable', 'url', 'max:500'],
            'social_links.linkedin' => ['nullable', 'url', 'max:500'],
            'social_links.youtube' => ['nullable', 'url', 'max:500'],
            'social_links.tiktok' => ['nullable', 'url', 'max:500'],

            // SEO Defaults
            'default_meta_description' => ['nullable', 'string', 'max:500'],
            'default_meta_keywords' => ['nullable', 'string', 'max:500'],
            'og_default_image' => ['nullable', 'string', 'max:500'],
            'twitter_handle' => ['nullable', 'string', 'max:100'],
            'twitter_card_type' => ['nullable', 'string', Rule::in(['summary', 'summary_large_image'])],

            // Organization Schema
            'organization_name' => ['nullable', 'string', 'max:255'],
            'organization_logo' => ['nullable', 'string', 'max:500'],
            'organization_type' => ['nullable', 'string', 'max:100'],
            'same_as' => ['nullable', 'array'],
            'same_as.*' => ['nullable', 'url', 'max:500'],

            // AI/Robots
            'llms_txt_content' => ['nullable', 'string', 'max:10000'],
            'llms_crawl_delay' => ['nullable', 'integer', 'min:0', 'max:3600'],
            'llms_allow_training' => ['nullable', 'boolean'],
            'robots_txt_content' => ['nullable', 'string', 'max:10000'],

            // Features
            'sitemap_enabled' => ['nullable', 'boolean'],
            'geo_enabled' => ['nullable', 'boolean'],

            // Scripts
            'head_scripts' => ['nullable', 'string', 'max:20000'],
            'body_scripts' => ['nullable', 'string', 'max:20000'],

            // Stripe
            'stripe_publishable_key' => ['nullable', 'string', 'max:255', 'starts_with:pk_'],
            'stripe_secret_key' => ['nullable', 'string', 'max:255', 'starts_with:sk_'],
            'stripe_webhook_secret' => ['nullable', 'string', 'max:255', 'starts_with:whsec_'],
            'stripe_test_mode' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'primary_color.regex' => 'Primary color must be a valid hex color (e.g., #3B82F6).',
            'secondary_color.regex' => 'Secondary color must be a valid hex color (e.g., #10B981).',
            'accent_color.regex' => 'Accent color must be a valid hex color (e.g., #F59E0B).',
            'background_color.regex' => 'Background color must be a valid hex color (e.g., #FFFFFF).',
            'text_color.regex' => 'Text color must be a valid hex color (e.g., #1F2937).',
            'success_color.regex' => 'Success color must be a valid hex color (e.g., #10B981).',
            'warning_color.regex' => 'Warning color must be a valid hex color (e.g., #F59E0B).',
            'error_color.regex' => 'Error color must be a valid hex color (e.g., #EF4444).',
            'info_color.regex' => 'Info color must be a valid hex color (e.g., #3B82F6).',
            'surface_color.regex' => 'Surface color must be a valid hex color (e.g., #FFFFFF).',
            'border_color.regex' => 'Border color must be a valid hex color (e.g., #E5E7EB).',
            'muted_color.regex' => 'Muted color must be a valid hex color (e.g., #6B7280).',
            'contact_email.email' => 'Please enter a valid email address.',
            'social_links.*.url' => 'Social link must be a valid URL.',
            'same_as.*.url' => 'Same-as links must be valid URLs.',
            'stripe_publishable_key.starts_with' => 'Publishable key must start with "pk_".',
            'stripe_secret_key.starts_with' => 'Secret key must start with "sk_".',
            'stripe_webhook_secret.starts_with' => 'Webhook secret must start with "whsec_".',
        ];
    }
}
