<?php

namespace App\Http\Requests\Pages;

use App\Models\Page;
use App\Services\PageService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates data for updating an existing page.
 */
class UpdatePageRequest extends FormRequest
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
        $pageId = $this->route('page')?->id;

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique('pages', 'slug')->ignore($pageId),
            ],
            'path' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique('pages', 'path')->ignore($pageId),
                function ($attribute, $value, $fail) {
                    $service = app(PageService::class);
                    if (!$service->isValidPath($value)) {
                        $fail('The path is reserved and cannot be used.');
                    }
                },
            ],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'string', 'max:500'],
            'twitter_card_type' => ['nullable', 'string', 'in:summary,summary_large_image'],
            'canonical_url' => ['nullable', 'url', 'max:500'],
            'no_index' => ['nullable', 'boolean'],
            'no_follow' => ['nullable', 'boolean'],
            'schema_type' => ['nullable', 'string', 'in:WebPage,Article,FAQPage,AboutPage,ContactPage'],
            'faqs' => ['nullable', 'array'],
            'faqs.*.question' => ['required_with:faqs', 'string', 'max:500'],
            'faqs.*.answer' => ['required_with:faqs', 'string', 'max:2000'],
            'speakable_selectors' => ['nullable', 'array'],
            'speakable_selectors.*' => ['string', 'max:255'],
            'priority' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'change_frequency' => ['nullable', 'string', 'in:always,hourly,daily,weekly,monthly,yearly,never'],
            'content' => ['nullable', 'array'],
            'sidebar_content' => ['nullable', 'array'],
            'template' => ['nullable', 'string', 'max:50'],
            'template_options' => ['nullable', 'array'],
            'template_options.*' => ['nullable'],
            'is_published' => ['nullable', 'boolean'],
            'show_in_nav' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'publish' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'A page title is required.',
            'slug.unique' => 'This slug is already in use by another page.',
            'path.unique' => 'This path is already in use by another page.',
        ];
    }
}
