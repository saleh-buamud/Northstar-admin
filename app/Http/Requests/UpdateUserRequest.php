<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:12', 'confirmed'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
