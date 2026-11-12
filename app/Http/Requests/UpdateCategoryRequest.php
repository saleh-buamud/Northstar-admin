<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:120', Rule::unique('categories', 'name')->ignore($this->route('category'))],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['sometimes', 'in:active,inactive'],
        ];
    }
}
