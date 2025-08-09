<?php

namespace App\Http\Requests\Admin;

use App\Rules\CloudinaryUrlValidatorRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VendorRequest extends FormRequest
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
        $id = 0;

        if ($this->route('id'))
            $id = $this->route('id');

        return [
            'country_id' => 'required|numeric|exists:countries,id',
            'name' => [
                'required',
                Rule::unique('vendors')->whereNull('deleted_at')->ignore($id),
            ],
            //'street' => 'required',
            //'serial_number' => 'required',
            'phone' => 'required',
            'logo' => ['nullable','url', new CloudinaryUrlValidatorRule()],
            'image_attach' => ['nullable','url', new CloudinaryUrlValidatorRule()],
            'email' => 'required',
            'is_service' => 'in:0,1',
            //'owner_name' => 'required',
            //'web' => 'required',
            //'mobile' => 'required',
            'city_id' => 'required|exists:cities,id',
            'region_id' => 'required|exists:regions,id',
            'status' => 'required|in:pending,approved,not_approved',
        ];
    }

    public function messages()
    {
        return [
            '*.required' => trans("admin.general_validation.required"),
        ];
    }
}
