<?php

namespace App\Http\Requests\Invitations;

use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates user invitation requests.
 */
class StoreInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                function ($attribute, $value, $fail) {
                    $hasPending = UserInvitation::where('email', $value)
                        ->pending()
                        ->exists();

                    if ($hasPending) {
                        $fail('A pending invitation already exists for this email address.');
                    }
                },
            ],
            'role' => ['required', 'string', 'exists:roles,name'],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'A user with this email address already exists.',
        ];
    }
}
