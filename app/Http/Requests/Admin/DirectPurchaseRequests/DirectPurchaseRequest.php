<?php

namespace App\Http\Requests\Admin\DirectPurchaseRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DirectPurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'vendor_id' => 'nullable|integer|exists:vendors,id',
            'priority_level' => ['nullable', Rule::in([1,2,3,4,5])],
        ];
    }

    // public function messages()
    // {
    //     return [
    //         '*.required' => trans("admin.general_validation.required"),
    //     ];
    // }
}
