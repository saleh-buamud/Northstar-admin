<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
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
        $role = $this->route('role');
        return [
            'name' => ['required', 'string', 'max:80', Rule::unique('roles', 'name')->ignore($role)],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'distinct', 'exists:permissions,id'],
        ];
    }
}
