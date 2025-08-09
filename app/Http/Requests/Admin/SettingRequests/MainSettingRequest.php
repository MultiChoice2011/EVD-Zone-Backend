<?php

namespace App\Http\Requests\Admin\SettingRequests;

use App\Rules\CloudinaryUrlValidatorRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone;

class MainSettingRequest extends FormRequest
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
            'phone' => ['required', 'string', (new Phone)->country(config('services.laravel_phone.countries'))],     // :SA,EG
            'email' => 'required|string|email',
            'main_image' => [Rule::when($this->isMethod('post'), ['nullable','url', new CloudinaryUrlValidatorRule()])],
            // 'maintenance_mode' => 'required|in:1,0',
            'dashboard_default_language' => 'required|integer',
            // 'website_default_language' => 'required|integer',
            'country_id' => 'required|integer',
            'city_id' => 'required|integer',
            'currency_id' => 'required|integer',
            // 'time_zone' => 'required|string',
            'store_name' => 'required|array',
            'store_name.*' => 'required',
            'manager_name' => 'required|array',
            'manager_name.*' => 'required',
            'address_line' => 'required|array',
            'address_line.*' => 'required',
            'meta_title' => 'required|array',
            'meta_title.*' => 'required',
            'meta_description' => 'required|array',
            'meta_description.*' => 'required|string',
            'meta_keywords' => 'required|array',
            'meta_keywords.*' => 'required|string',
            'unavailable_languages' => 'array',
            'unavailable_languages.*' => 'required|integer',
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
