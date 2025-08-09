<?php

namespace App\Http\Requests\Admin\VendorProductRequests;

use App\Enums\VendorProductType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VendorProductRequest extends FormRequest
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
        $id = 0;

        if ($this->route('id'))
            $id = $this->route('id');

        return [
            'vendor_id' => ['required', Rule::exists('vendors', 'id')],
            'product_id' => ['required', Rule::exists('products', 'id')],
            'type' => ['required', Rule::in(VendorProductType::getList())],
            'vendor_product_id' => 'required|integer',
            'provider_cost' => 'nullable|numeric|min:0'
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
