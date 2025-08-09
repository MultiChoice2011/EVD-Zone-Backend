<?php

namespace App\Http\Requests\Admin\TaxRequests;

use App\Enums\GeneralStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaxNumberRequest extends FormRequest
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
            'tax_number' => 'required|string',
            'show_tax_number' => 'required|in:0,1',
            'tax_files' => ['array'],
            'tax_files.*' => ['file', 'max:1048576', 'mimes:jpeg,png,jpg,pdf']
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
