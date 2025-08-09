<?php

namespace App\Http\Requests\Admin\BrandRequests;

use App\Enums\BrandImageKey;
use App\Rules\AllLanguagesRequired;
use App\Rules\CloudinaryUrlValidatorRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
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
            'name.*' => ['required','string','min:2','max:191',Rule::unique('brand_translations', 'name')->ignore($id,'brand_id')],
            'description' => 'nullable|array',
            'description.*' => ['required','string','min:2'],
            'status' => 'required|in:active,inactive',
            'images_data' => 'nullable|array',
            'images_data.*.key' => ['nullable', Rule::in(BrandImageKey::getList())],
            'images_data.*.image' => ['required','url', new CloudinaryUrlValidatorRule()],
            // 'images_data.*.image' => 'required|image|max:1048576',
        ];
    }

    public function messages()
    {
        return [
            "*.required" => trans("admin.general_validation.required"),
        ];
    }
}
