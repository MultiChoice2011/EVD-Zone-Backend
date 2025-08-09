<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RegionRequest extends FormRequest
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
            'country_id' => "required",
        ];
    }

    public function messages()
    {
        return [
            'name.*.required' => trans("admin.regions.validations.name_required"),
            'country_id.required' => trans("admin.regions.validations.country_required"),
        ];
    }
}
