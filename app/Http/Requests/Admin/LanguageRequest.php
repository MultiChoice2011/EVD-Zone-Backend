<?php

namespace App\Http\Requests\Admin;

use App\Rules\CloudinaryUrlValidatorRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LanguageRequest extends FormRequest
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
    public function rules()
    {
        $id = 0;

        if ($this->route('language'))
            $id = $this->route('language');

        return [
            'name' => "required|string|min:2|max:191",
            'code' => "required|string|min:2",
            'sort_order' => "required|int",
            'image' => Rule::when($this->isMethod('post'), ['nullable','url', new CloudinaryUrlValidatorRule()]),
            // 'image' => Rule::when($this->isMethod('post'),'image|max:1048576'),
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages()
    {
        return [
            "*.required" => trans("admin.general_validation.required"),
            "*.string" => trans("admin.general_validation.string"),
            "*.min" => trans("admin.general_validation.min_2"),
        ];
    }
}
