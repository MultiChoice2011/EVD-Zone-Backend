<?php

namespace App\Http\Requests\Seller\CategoryRequests;

use Illuminate\Foundation\Http\FormRequest;

class SubcategoryRequest extends FormRequest
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
            'parent_id' => 'nullable|integer',
            'is_brands' => 'nullable|in:0,1',
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
        ];
    }
}
