<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CityRequest extends FormRequest
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
            'region_id' => "required",
        ];
    }

    public function messages()
    {
        return [
            'name.*.required' => trans("admin.cities.validations.name_required"),
            'region_id.required' => trans("admin.cities.validations.region_required"),
        ];
    }
}
