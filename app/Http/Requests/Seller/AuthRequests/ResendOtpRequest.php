<?php

namespace App\Http\Requests\Seller\AuthRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone;

class ResendOtpRequest extends FormRequest
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
            'phone' => ['required',(new Phone)->country(config('services.laravel_phone.countries'))],    // :SA,EG
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
