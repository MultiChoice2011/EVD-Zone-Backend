<?php

namespace App\Http\Requests\Admin;

use App\Rules\AllLanguagesRequired;
use App\Rules\CloudinaryUrlValidatorRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SellerGroupRequest extends FormRequest
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
        $id = $this->route('id') ?? 0;
        return [
            'name' => ['required','array',new AllLanguagesRequired()],
            'name.*' => ['required','string','min:2','max:191',Rule::unique('seller_group_translations', 'name')->ignore($id,'seller_group_id')],
            'parent_id' => 'nullable|numeric|exists:seller_groups,id',
            'image' => ['nullable','url', new CloudinaryUrlValidatorRule()],
            // 'image' => 'image|max:1048576',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages()
    {
        return [
            'name.*.required' => trans("admin.general_validation.required"),
            "parent_id.numeric" => trans("admin.general_validation.number"),
            "parent_id.exists" => trans("admin.general_validation.exists"),
        ];
    }
}
