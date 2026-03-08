<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates site-level SEO and GEO settings.
 */
class UpdateSiteSeoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage-settings');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Default Meta
            'default_meta_description' => ['nullable', 'string', 'max:500'],
            'default_meta_keywords' => ['nullable', 'string', 'max:255'],

            // Open Graph Defaults
            'og_default_image' => ['nullable', 'string', 'max:500'],

            // Twitter
            'twitter_handle' => ['nullable', 'string', 'max:50', 'regex:/^@?[a-zA-Z0-9_]+$/'],
            'twitter_card_type' => ['nullable', 'string', Rule::in([
                'summary',
                'summary_large_image',
                'app',
                'player',
            ])],

            // Organization Schema
            'organization_name' => ['nullable', 'string', 'max:255'],
            'organization_logo' => ['nullable', 'string', 'max:500'],
            'organization_type' => ['nullable', 'string', Rule::in([
                'Organization',
                'Corporation',
                'LocalBusiness',
                'Restaurant',
                'Store',
                'MedicalBusiness',
                'LegalService',
                'RealEstateAgent',
                'FinancialService',
                'EducationalOrganization',
                'GovernmentOrganization',
                'NonProfit',
                'SportsOrganization',
            ])],
            'same_as' => ['nullable', 'array', 'max:20'],
            'same_as.*' => ['url', 'max:500'],

            // LLMs.txt (GEO)
            'llms_txt_content' => ['nullable', 'string', 'max:10000'],
            'llms_crawl_delay' => ['nullable', 'integer', 'min:0', 'max:3600'],
            'llms_allow_training' => ['nullable', 'boolean'],

            // Robots.txt
            'robots_txt_content' => ['nullable', 'string', 'max:5000'],

            // Feature Flags
            'sitemap_enabled' => ['nullable', 'boolean'],
            'geo_enabled' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom attribute names for error messages.
     */
    public function attributes(): array
    {
        return [
            'default_meta_description' => 'default meta description',
            'default_meta_keywords' => 'default meta keywords',
            'og_default_image' => 'default Open Graph image',
            'twitter_handle' => 'Twitter handle',
            'twitter_card_type' => 'Twitter card type',
            'organization_name' => 'organization name',
            'organization_logo' => 'organization logo',
            'organization_type' => 'organization type',
            'same_as' => 'social profile URLs',
            'llms_txt_content' => 'LLMs.txt content',
            'llms_crawl_delay' => 'LLMs crawl delay',
            'llms_allow_training' => 'allow AI training',
            'robots_txt_content' => 'robots.txt content',
            'sitemap_enabled' => 'sitemap setting',
            'geo_enabled' => 'GEO setting',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'twitter_handle.regex' => 'The Twitter handle must be a valid Twitter username.',
            'same_as.*.url' => 'Each social profile URL must be a valid URL.',
            'llms_crawl_delay.max' => 'The crawl delay must not exceed 3600 seconds (1 hour).',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('twitter_handle')) {
            $handle = $this->input('twitter_handle');
            if ($handle && !str_starts_with($handle, '@')) {
                $this->merge(['twitter_handle' => '@' . $handle]);
            }
        }
    }
}
