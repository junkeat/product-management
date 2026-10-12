<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id'  => ['nullable', 'integer', 'exists:categories,id'],
            'min_price'    => ['nullable', 'numeric', 'min:0'],
            'max_price'    => ['nullable', 'numeric', 'min:0', 'gte:min_price'],
            'min_stock'    => ['nullable', 'integer', 'min:0'],
            'max_stock'    => ['nullable', 'integer', 'min:0', 'gte:min_stock'],
            'stock_status' => ['nullable', 'in:in_stock,out_of_stock,low_stock'],
            'is_active'    => ['nullable', 'boolean'],
            'search'       => ['nullable', 'string', 'max:100'],
            'sort'         => ['nullable', 'in:name,price,stock,created_at'],
            'direction'    => ['nullable', 'in:asc,desc'],
            'per_page'     => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
