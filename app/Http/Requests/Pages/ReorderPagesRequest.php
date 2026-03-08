<?php

namespace App\Http\Requests\Pages;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates data for reordering pages.
 */
class ReorderPagesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('pages.update');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'page_ids' => ['required', 'array', 'min:1'],
            'page_ids.*' => ['required', 'integer', 'exists:pages,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'page_ids.required' => 'Page IDs are required for reordering.',
            'page_ids.*.exists' => 'One or more pages do not exist.',
        ];
    }
}
