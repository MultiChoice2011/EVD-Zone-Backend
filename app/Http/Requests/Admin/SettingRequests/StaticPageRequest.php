<?php

namespace App\Http\Requests\Admin\SettingRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaticPageRequest extends FormRequest
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
            'title' => 'required|array',
            'title.*' => 'required|string',
            'meta_title' => 'required|array',
            'meta_title.*' => 'required|string',
            'meta_description' => 'required|array',
            'meta_description.*' => 'required|string',
            'meta_keywords' => 'required|array',
            'meta_keywords.*' => 'required|string',
            'content' => 'required|array',
            'content.*' => 'required|string',
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
