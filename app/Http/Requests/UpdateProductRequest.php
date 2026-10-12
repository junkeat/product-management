<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'sku' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($this->route('product'))
            ],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0', 'max:99999999.99'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'supplier_ids' => ['nullable', 'array'],
            'supplier_ids.*' => ['integer', 'exists:suppliers,id'],
        ];
    }
}
