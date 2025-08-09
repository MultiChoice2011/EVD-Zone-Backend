<?php

namespace App\Http\Requests\Seller\ProductRequests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AccountOptionDetailsRequest extends FormRequest
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
        $id = 0;

        if ($this->route('id'))
            $id = $this->route('id');

        return [
            'product_id' => 'required|integer',
            'category_id' => 'required|integer',
            'product_options' => 'required|array',
            'product_options.*.id' => 'required|integer',
            'product_options.*.option_value_ids' => 'required_if:product_options.*.value,null|nullable|array',
            'product_options.*.value' => 'required_if:product_options.*.option_value_ids,null|nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'product_options.*.option_value_ids.required_if' => __("validation.all_options_required"),
            'product_options.*.value.required_if' => __("validation.all_options_required"),
        ];

    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors(),
        ], 422);

        throw new HttpResponseException($response);
    }

}
