<?php

namespace App\Http\Requests\Pages;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates SEO-specific fields when updating a page.
 */
class UpdatePageSeoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('page'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Basic Meta
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],

            // Open Graph
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'string', 'max:500'],

            // Twitter
            'twitter_card_type' => ['nullable', 'string', Rule::in([
                'summary',
                'summary_large_image',
                'app',
                'player',
            ])],

            // Canonical & Indexing
            'canonical_url' => ['nullable', 'url', 'max:500'],
            'no_index' => ['nullable', 'boolean'],
            'no_follow' => ['nullable', 'boolean'],

            // Structured Data
            'schema_type' => ['nullable', 'string', Rule::in([
                'WebPage',
                'Article',
                'BlogPosting',
                'NewsArticle',
                'AboutPage',
                'ContactPage',
                'FAQPage',
                'Product',
                'Service',
                'LocalBusiness',
            ])],

            // FAQs
            'faqs' => ['nullable', 'array', 'max:50'],
            'faqs.*.question' => ['required_with:faqs', 'string', 'max:500'],
            'faqs.*.answer' => ['required_with:faqs', 'string', 'max:2000'],

            // Speakable (GEO)
            'speakable_selectors' => ['nullable', 'array', 'max:20'],
            'speakable_selectors.*' => ['string', 'max:100'],

            // Sitemap
            'priority' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'change_frequency' => ['nullable', 'string', Rule::in([
                'always',
                'hourly',
                'daily',
                'weekly',
                'monthly',
                'yearly',
                'never',
            ])],
        ];
    }

    /**
     * Get custom attribute names for error messages.
     */
    public function attributes(): array
    {
        return [
            'og_title' => 'Open Graph title',
            'og_description' => 'Open Graph description',
            'og_image' => 'Open Graph image',
            'twitter_card_type' => 'Twitter card type',
            'canonical_url' => 'canonical URL',
            'no_index' => 'noindex setting',
            'no_follow' => 'nofollow setting',
            'schema_type' => 'schema type',
            'faqs' => 'FAQ items',
            'faqs.*.question' => 'FAQ question',
            'faqs.*.answer' => 'FAQ answer',
            'speakable_selectors' => 'speakable selectors',
            'change_frequency' => 'change frequency',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'faqs.*.question.required_with' => 'Each FAQ item must have a question.',
            'faqs.*.answer.required_with' => 'Each FAQ item must have an answer.',
            'priority.min' => 'Priority must be between 0 and 1.',
            'priority.max' => 'Priority must be between 0 and 1.',
        ];
    }
}
