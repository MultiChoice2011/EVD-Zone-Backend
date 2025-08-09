<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class Verify2FAuthRequest extends FormRequest
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
        return [
            'name' => 'required|string',
            'owner_name' => 'required|string|unique:sellers,owner_name',
            'email' => 'required|email|unique:sellers,email',
            'country_id' => 'required|integer|exists:countries,id',
            'city_id' => 'required|integer|exists:cities,id',
            'phone' => 'required|string|unique:sellers,phone',
            'password' => 'required|string|confirmed|min:8',
            'google2fa_secret' => 'required|string',
            'otp' => 'required|numeric|digits:6'
        ];
    }
}
