<?php

namespace App\Http\Requests\Admin\ProductRequests;

use App\Rules\AllLanguagesRequired;
use App\Rules\CloudinaryUrlValidatorRule;
use App\Rules\RequiredProductOption;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ProductRequest extends FormRequest
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
    public function rules(Request $request)
    {
        $id = 0;

        if ($this->route('product'))
            $id = $this->route('product');

        return [
            'name' => ['required','array',new AllLanguagesRequired()],
            'name.*' => ['required','string','min:2','max:191'],
            'category_ids' => "required|array",
            'category_ids.*' => "required|exists:categories,id",
            'vendor_id' => "nullable|exists:vendors,id",
            'brand_id' => "required|exists:brands,id",
            'type' => "required",
            'serial' => "sometimes|string",
            'quantity' => "sometimes|integer|min:0|max:1000000",
            'cost_price' => "sometimes|numeric",
            'wholesale_price' => "sometimes|numeric|gte:cost_price",
            'price' =>  "sometimes|numeric|gte:wholesale_price|gte:cost_price",
            'coins_number' =>  "required|numeric|min:0",
            'min_coins' => "required|integer|min:0",
            'max_coins' => "required|integer|min:0|gte:min_coins",
            // 'status' => "required",
            // 'web' => "sometimes|integer",
            // 'mobile' => "sometimes|integer",
            'sku' => "sometimes|numeric",
            'notify' => "sometimes|integer",
            'max_quantity' => "sometimes|integer",
            'minimum_quantity' => "sometimes|integer|lt:max_quantity",
            'sort_order' =>  "sometimes|integer",
            'is_live_integration' =>  "sometimes|in:0,1",
            'image' => ['nullable','url', new CloudinaryUrlValidatorRule()],
            'images' => 'nullable|array',
            'images.*' => ['required','url', new CloudinaryUrlValidatorRule()],

            'product_options' =>  [new RequiredProductOption($request->get('category_ids')), 'array'],
            // 'product_options.*.values.*.price' =>  ['sometimes','numeric','gte:cost_price'],
        ];
    }

    public function messages()
    {
        return [
            '*.required' => trans("admin.general_validation.required"),
            "*.exists" => trans("admin.general_validation.exists"),
            'minimum_quantity.lt' => 'The minimum quantity must be less than the maximum quantity.',
            'cost_price.lt' => 'The cost price must be less than the wholesale price and the price.',
        ];
    }
}
