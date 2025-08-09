<?php

namespace App\Http\Requests\Admin\SubAdminRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubAdminRequest extends FormRequest
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
            'name' => 'required|max:255',
            'email' => ['required','email',Rule::unique('users', 'email')->ignore($id)],
            'phone' => ['required',Rule::unique('users', 'phone')->ignore($id)],
            'password' => ['required_if:id,'.$id,'string', 'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[^\w\s]).{8,}$/'],
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ];
    }
}
