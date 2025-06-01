<?php

namespace App\Infrastructure\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'tenant_id.required' => 'The tenant ID is required.',
            'tenant_id.exists' => 'The selected tenant does not exist.',
            'name.required' => 'The name is required.',
            'email.required' => 'The email is required.',
            'email.email' => 'The email must be a valid email address.',
            'phone.required' => 'The phone number is required.',
        ];
    }
}
