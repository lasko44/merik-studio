<?php

namespace App\Http\Requests\Navigation;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates navigation structure updates.
 */
class UpdateNavigationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('updateNavigation', Page::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array'],
            'items.*.id' => ['required', function ($attribute, $value, $fail) {
                // Allow 'blog' and 'products' as special item identifiers
                if ($value === 'blog' || $value === 'products') {
                    return;
                }
                // Otherwise must be a valid page ID
                if (!is_int($value) && !ctype_digit((string) $value)) {
                    $fail('The ' . $attribute . ' must be an integer, "blog", or "products".');
                    return;
                }
                if (!Page::where('id', $value)->exists()) {
                    $fail('The selected ' . $attribute . ' is invalid.');
                }
            }],
            'items.*.parent_id' => ['nullable', 'integer', 'exists:pages,id'],
            'items.*.nav_order' => ['required', 'integer', 'min:0'],
            'items.*.show_in_nav' => ['required', 'boolean'],
        ];
    }
}
