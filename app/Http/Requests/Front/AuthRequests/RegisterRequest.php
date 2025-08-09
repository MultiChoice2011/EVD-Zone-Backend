<?php

namespace App\Http\Requests\Front\AuthRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:150',
            'owner_name' => ['required', 'string', Rule::unique('sellers', 'owner_name')->ignore($id)],
            'email' => ['required','email', Rule::unique('sellers', 'email')->ignore($id)],
            // 'phone' => ['required', 'string', Rule::unique('sellers', 'phone')->ignore($id, 'id')],
            'password' => 'required|string',
            'google2fa_secret' => 'required|string|size:16',
            'device_id' => 'nullable|string',
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
