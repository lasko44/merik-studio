<?php

namespace App\Http\Requests\Setup;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates setup wizard form data for site generation.
 */
class GenerateSiteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Step 1: Business Info
            'businessName' => ['required', 'string', 'max:255'],
            'industry' => ['required', 'string', Rule::in(array_keys(config('ai.industries', [])))],
            'description' => ['required', 'string', 'max:1000'],
            'targetAudience' => ['nullable', 'string', 'max:500'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'yearsInBusiness' => ['nullable', 'string', Rule::in(['', 'new', '1-2', '3-5', '5-10', '10-20', '20+'])],
            'location' => ['nullable', 'string', 'max:255'],

            // Step 2: Goals
            'primaryGoal' => ['required', 'string', Rule::in(['leads', 'sales', 'information', 'portfolio', 'community'])],
            'secondaryGoals' => ['nullable', 'array'],
            'secondaryGoals.*' => ['string'],
            'callToAction' => ['nullable', 'string', 'max:255'],

            // Step 3: Design
            'style' => ['required', 'string', Rule::in(['modern', 'classic', 'minimal', 'bold'])],
            'colorMood' => ['required', 'string', Rule::in(['professional', 'friendly', 'energetic', 'calm', 'luxury'])],
            'primaryColor' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo' => ['nullable', 'string', 'max:500'],

            // Step 4: Features
            'features' => ['nullable', 'array'],
            'features.blog' => ['nullable', 'boolean'],
            'features.products' => ['nullable', 'boolean'],
            'features.contactForm' => ['nullable', 'boolean'],
            'features.testimonials' => ['nullable', 'boolean'],
            'features.faq' => ['nullable', 'boolean'],
            'features.team' => ['nullable', 'boolean'],
            'features.portfolio' => ['nullable', 'boolean'],
            'features.newsletter' => ['nullable', 'boolean'],

            // Step 5: Pages
            'pages' => ['required', 'array', 'min:1'],
            'pages.*' => ['string', 'max:100'],
            'customPages' => ['nullable', 'array'],
            'customPages.*' => ['nullable', 'string', 'max:100'],

            // Step 6: Content - Detailed business information
            'services' => ['required', 'array', 'min:1'],
            'services.*' => ['required', 'string', 'min:1', 'max:500'],
            'companyStory' => ['nullable', 'string', 'max:1500'],
            'achievements' => ['nullable', 'string', 'max:1000'],
            'whyChooseUs' => ['nullable', 'string', 'max:1000'],
            'processSteps' => ['nullable', 'string', 'max:1000'],
            'faqItems' => ['nullable', 'string', 'max:2000'],
            'testimonialNames' => ['nullable', 'string', 'max:500'],

            // Legacy content fields (kept for backwards compatibility)
            'sellingPoints' => ['nullable', 'array', 'max:5'],
            'sellingPoints.*' => ['nullable', 'string', 'max:255'],
            'valueProposition' => ['nullable', 'string', 'max:500'],
            'additionalContent' => ['nullable', 'string', 'max:2000'],

            // Contact info
            'contactEmail' => ['nullable', 'email', 'max:255'],
            'contactPhone' => ['nullable', 'string', 'max:50'],
            'contactAddress' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'businessName.required' => 'Please enter your business name.',
            'industry.required' => 'Please select an industry.',
            'description.required' => 'Please describe what your business does.',
            'primaryGoal.required' => 'Please select your primary goal.',
            'style.required' => 'Please select a design style.',
            'colorMood.required' => 'Please select a color mood.',
            'pages.required' => 'Please select at least one page to create.',
            'pages.min' => 'Please select at least one page to create.',
            'primaryColor.regex' => 'Please enter a valid hex color code (e.g., #FF5733).',
            'services.required' => 'Please add at least one service or product.',
            'services.min' => 'Please add at least one service or product.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure pages array always includes 'home'
        $pages = $this->input('pages', []);
        if (!in_array('home', $pages)) {
            array_unshift($pages, 'home');
        }
        $this->merge(['pages' => $pages]);

        // Convert feature values to booleans
        if ($this->has('features')) {
            $features = $this->input('features', []);
            foreach ($features as $key => $value) {
                $features[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }
            $this->merge(['features' => $features]);
        }

        // Filter empty services but keep at least the non-empty ones for validation
        if ($this->has('services')) {
            $services = $this->input('services', []);
            $filteredServices = array_values(array_filter($services, fn($s) => !empty(trim($s ?? ''))));
            // If all services are empty, keep at least one empty string so validation fails properly
            $this->merge(['services' => count($filteredServices) > 0 ? $filteredServices : ['']]);
        }
    }

    /**
     * Get the validated data with defaults applied.
     */
    public function validatedWithDefaults(): array
    {
        $validated = $this->validated();

        // Apply defaults for arrays
        $validated['features'] = $validated['features'] ?? [];
        $validated['secondaryGoals'] = $validated['secondaryGoals'] ?? [];
        $validated['customPages'] = array_filter($validated['customPages'] ?? []);
        $validated['sellingPoints'] = array_filter($validated['sellingPoints'] ?? []);
        $validated['services'] = array_filter($validated['services'] ?? [], fn($s) => !empty(trim($s)));

        // Apply defaults for optional string fields
        $validated['tagline'] = $validated['tagline'] ?? '';
        $validated['yearsInBusiness'] = $validated['yearsInBusiness'] ?? '';
        $validated['location'] = $validated['location'] ?? '';
        $validated['companyStory'] = $validated['companyStory'] ?? '';
        $validated['achievements'] = $validated['achievements'] ?? '';
        $validated['whyChooseUs'] = $validated['whyChooseUs'] ?? '';
        $validated['processSteps'] = $validated['processSteps'] ?? '';
        $validated['faqItems'] = $validated['faqItems'] ?? '';
        $validated['testimonialNames'] = $validated['testimonialNames'] ?? '';

        return $validated;
    }
}
