<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;
use Propaganistas\LaravelPhone\Rules\Phone;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string',
            'owner_name' => 'required|string|unique:sellers,owner_name',
            'email' => 'required|email|unique:sellers,email',
            'password' => 'nullable|string|confirmed|min:8',
            'currency_id' => 'nullable|integer|exists:currencies,id',
            'country_id' => 'required|integer|exists:countries,id',
            'city_id' => 'required|integer|exists:cities,id',
            'phone' => ['required','regex:/^\d+$/',(new Phone)->country(config('services.laravel_phone.countries')), Rule::unique('sellers', 'phone')->ignore($id)],
        ];
    }
}
