<?php

namespace App\Http\Requests\Admin;

use App\Rules\CloudinaryUrlValidatorRule;
use Illuminate\Foundation\Http\FormRequest;

class CountryRequest extends FormRequest
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
            'name.*' => "required|string",
            'code' => "required|string",
            'flag' => ['nullable','url', new CloudinaryUrlValidatorRule()],
            // 'flag' => 'image|max:1048576',
        ];
    }

    public function messages()
    {
        return [
            'name.*.required' => trans("admin.countries.validations.name_required"),
            'code.required' => trans("admin.countries.validations.code_required"),
        ];
    }
}
