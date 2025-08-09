<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone;
class CreateAdminListRequest extends FormRequest
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
            'email' => 'required|email|unique:sellers,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
            'phone' => [
                'required','regex:/^\d+$/',(new Phone)->country(config('services.laravel_phone.countries')), Rule::unique('sellers', 'phone')
            ],
            'owner_name' => 'nullable|string',
            'logo' => 'nullable|mimes:png,jpg|max:2048',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
            'parent_id' => 'nullable|exists:sellers,id'
        ];
    }
}
