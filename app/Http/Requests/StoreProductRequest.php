<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:180'],
            'sku' => ['required', 'string', 'max:80', 'unique:products,sku'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price', 'max:9999999999.99'],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'status' => ['required', 'in:active,inactive,draft'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
