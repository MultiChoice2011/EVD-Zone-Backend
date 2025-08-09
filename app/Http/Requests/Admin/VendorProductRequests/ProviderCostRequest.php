<?php

namespace App\Http\Requests\Admin\VendorProductRequests;

use App\Enums\VendorProductType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProviderCostRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'vendor_id' => 'required|min:1',
            'type' => ['required', Rule::in(VendorProductType::getList())],
            'vendor_product_id' => 'required|string',
        ];
    }

    /*
    public function messages()
    {

        return [
            'name.*.required' => trans("validation.required"),
            "description.required" => trans("validation.required"),
        ];

    }
    */
}
