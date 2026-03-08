<?php

namespace App\Http\Requests\Products;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates data for creating a new product.
 */
class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Product::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'image' => ['nullable', 'string', 'max:500'],
            'gallery_images' => ['nullable', 'array', 'max:10'],
            'gallery_images.*' => ['string', 'max:500'],
            'category_id' => ['nullable', 'integer', 'exists:product_categories,id'],
            'sync_to_stripe' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'type' => ['nullable', 'string', Rule::in(['one_time', 'recurring'])],
            'recurring_interval' => ['nullable', 'string', Rule::in(['month', 'year']), 'required_if:type,recurring'],
            'track_inventory' => ['nullable', 'boolean'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'content' => ['nullable', 'array'],
            'content.*.id' => ['required', 'string'],
            'content.*.type' => ['required', 'string'],
            'content.*.data' => ['required', 'array'],
            'publish' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'A product name is required.',
            'price.required' => 'A product price is required.',
            'price.min' => 'Price must be zero or greater.',
            'slug.unique' => 'This slug is already in use by another product.',
            'recurring_interval.required_if' => 'A recurring interval is required for recurring products.',
        ];
    }
}
