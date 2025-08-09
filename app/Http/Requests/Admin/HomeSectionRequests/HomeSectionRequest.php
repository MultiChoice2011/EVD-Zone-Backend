<?php

namespace App\Http\Requests\Admin\HomeSectionRequests;

use App\Enums\GeneralStatusEnum;
use App\Enums\HomeSectionType;
use App\Rules\CloudinaryUrlValidatorRule;
use App\Rules\RedirectUrlRequired;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class HomeSectionRequest extends FormRequest
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
    public function rules(Request $request): array
    {
        $id = $this->route('id') ?? 0;
        $imageRules = 'nullable|array';
        if ($this->route()->getName() === 'api.admin.settings.storeAppearance.homeSections.store')
            $imageRules = 'required_if:type,banner|array';

        return [
            'type' => ['required', Rule::in(HomeSectionType::getList())],
            'display_in' => 'required|in:1,2',
            'style' => 'required_if:type,category',
            'categories' => 'required_if:type,category|array',
            'categories.*' => ['required', Rule::exists('categories', 'id')],
            'brands' => 'required_if:type,category|array',
            'brands.*' => ['required', Rule::exists('brands', 'id')],
            'name' => 'required|array',
            'name.*' => 'required',
            'title' => 'required|array',
            'title.*' => 'required',
            'display' => 'required|array',
            'display.*' => 'required|in:0,1',
            'redirect_url' => ['array', new RedirectUrlRequired($request->all())],
            'redirect_url.*' => 'required|url',
            'images' => $imageRules,
            'images.*' => ['required','url', new CloudinaryUrlValidatorRule()],
            // 'images.*' => 'required|file|mimes:jpg,png,jpeg|max:1048576',
            'brand_id' => ['nullable', Rule::exists('brands', 'id')],
            'banner_category_id' => ['nullable', Rule::exists('categories', 'id')]
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
