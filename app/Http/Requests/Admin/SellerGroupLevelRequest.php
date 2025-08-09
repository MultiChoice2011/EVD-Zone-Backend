<?php

namespace App\Http\Requests\Admin;

use App\Rules\CloudinaryUrlValidatorRule;
use Illuminate\Foundation\Http\FormRequest;

class SellerGroupLevelRequest extends FormRequest
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

        if ($this->route('sellerGroupLevel'))
            $id = $this->route('sellerGroupLevel');

        return [
            'name.*' => "required|string",
            'parent_id' => 'nullable|numeric|exists:sellerGroups,id',
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
