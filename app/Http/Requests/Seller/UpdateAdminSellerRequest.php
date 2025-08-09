<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone;
class UpdateAdminSellerRequest extends FormRequest
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
            'email' => ['nullable','email',Rule::unique('sellers', 'email')->ignore($id)],
            'role' => 'nullable|string|exists:roles,name',
            'phone' => [
                'required',
                'string',
                (new Phone)->country(config('services.laravel_phone.countries')),
                Rule::unique('sellers', 'phone')->ignore($id)
            ],
            'logo' => 'nullable|mimes:png,jpg|max:2048',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ];
    }
}
