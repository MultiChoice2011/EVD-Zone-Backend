<?php

namespace App\Http\Requests\Seller\RoleRequest;

use App\Rules\AllLanguagesRequired;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class StoreRequest extends FormRequest
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
            "name" => ['required', ValidationRule::unique('roles', 'name')->ignore($this->route('id'))],
            "permissions" => ['required', ValidationRule::exists('permissions', 'name')],
            'display_name' => ['required','array',new AllLanguagesRequired()],
            'display_name.*' => 'string',
            'status' => 'required|string|in:active,inactive'
        ];
    }
}
