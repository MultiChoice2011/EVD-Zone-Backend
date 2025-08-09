<?php

namespace App\Http\Requests\Admin\FileRequests;

use App\Rules\CloudinaryUrlValidatorRule;
use Illuminate\Foundation\Http\FormRequest;

class DestroyFileRequest extends FormRequest
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
            'folder_name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', new CloudinaryUrlValidatorRule()],
        ];
    }
}
