<?php

namespace App\Http\Requests\Seller\ProductRequests;

use Illuminate\Foundation\Http\FormRequest;

class ProductListRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
            'category_id' => 'nullable|integer',
            'brand_id' => 'nullable|integer',
            'search' => ['nullable', 'string', 'max:255', 'regex:/^[\p{L}\p{N}\s\$_\-]+$/u'],
        ];
    }
}
