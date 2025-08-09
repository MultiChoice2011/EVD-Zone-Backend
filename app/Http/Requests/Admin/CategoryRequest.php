<?php

namespace App\Http\Requests\Admin;

use App\Enums\GeneralStatusEnum;
use App\Rules\AllLanguagesRequired;
use App\Rules\CloudinaryUrlValidatorRule;
use App\Rules\UniqueIgnoreSoftDeleted;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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

        $nameRules = [
            'required',
            'string',
            'min:2',
            'max:191'
        ];

        // Conditionally add the UniqueIgnoreSoftDeleted rule when parent_id is null
        if ($this->input('parent_id') === null) {
            $nameRules[] = new UniqueIgnoreSoftDeleted('categories', 'category_translations', 'name', 'category_id', $id);
            // Rule::unique('category_translations', 'name')->ignore($id,'category_id')
        }

        return [
            'name' => ['required','array',new AllLanguagesRequired()],
            'name.*' => $nameRules,
            'parent_id' => 'nullable|numeric|exists:categories,id',
            'brand_id' => ['nullable', Rule::exists('brands', 'id')->where(function ($query) {$query->where('status', GeneralStatusEnum::getStatusActive());})],
            'image' => ['nullable','url', new CloudinaryUrlValidatorRule()],
            // 'image' => 'image|max:1048576',
            'meta_title' => ['required','array',new AllLanguagesRequired()],
            'meta_title.*' => ['required','string','min:2','max:191'],
            // 'status' => 'required|in:active,inactive',
            // 'web' => 'required|boolean',
            // 'mobile' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.*.required' => trans("admin.categories.validations.name_ar_required"),
            "parent_id.numeric" => trans("admin.categories.validations.parent_id_numeric"),
            "parent_id.exists" => trans("admin.categories.validations.parent_id_exists"),
        ];
    }
}
