<?php

namespace App\Http\Requests\Admin\TaxRequests;

use App\Enums\GeneralStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePricesDisplayRequest extends FormRequest
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
            'prices_include_tax' => 'required|in:0,1'
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
