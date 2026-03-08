<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates Stripe checkout session creation requests.
 */
class CreateCheckoutSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public checkout
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'price_id' => ['required', 'string'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:99'],
            'success_url' => ['sometimes', 'url'],
            'cancel_url' => ['sometimes', 'url'],
        ];
    }

    /**
     * Get the quantity with default value.
     */
    public function getQuantity(): int
    {
        return (int) $this->input('quantity', 1);
    }

    /**
     * Get the success URL with default value.
     */
    public function getSuccessUrl(): string
    {
        return $this->input('success_url', route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}');
    }

    /**
     * Get the cancel URL with default value.
     */
    public function getCancelUrl(): string
    {
        return $this->input('cancel_url', route('checkout.cancel'));
    }
}
