<?php

namespace App\Http\Requests\Admin;

use App\Enums\Integration\IntegrationTypeEnum;
use App\Rules\AllLanguagesRequired;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OptionRequest extends FormRequest
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
        if ($this->route('id'))
            $id = $this->route('id');

        return [
            'name' => ['required','array',new AllLanguagesRequired()],
            'name.*' => ["required", "string", Rule::unique('option_translations', 'name')->ignore($id, 'option_id')],
            'key' => ["required", Rule::exists('integration_option_keys', 'key')->where('type', IntegrationTypeEnum::getOptionType())],
            'option_values' => ['sometimes','array'],
            'option_values.*.name' => ['required','array',new AllLanguagesRequired()],
            'option_values.*.name.*' => ["required","string"],
            'option_values.*.key' => ["required", Rule::exists('integration_option_keys', 'key')->where('type', IntegrationTypeEnum::getOptionValueType())],
            'type' => "required|in:file,image,select,radio,checkbox,text,date,textarea",
        ];
    }

    public function messages()
    {
        return [
            'name.*.required' => trans("admin.attributeGroups.validations.name_required"),
            'option_values.*.name.required' => trans("admin.attributeGroups.validations.name_required"),
        ];
    }
}
