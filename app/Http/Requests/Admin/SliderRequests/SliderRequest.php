<?php

namespace App\Http\Requests\Admin\SliderRequests;

use App\Enums\GeneralStatusEnum;
use App\Rules\CloudinaryUrlValidatorRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SliderRequest extends FormRequest
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
        $id = $this->route('id') ?? 0;
        $imageRules = 'nullable|array';
        if ($this->route()->getName() === 'api.admin.settings.storeAppearance.sliders.store')
            $imageRules = 'required|array';

        Log::info($this->route()->getName());
        return [
            //'order' => 'required|integer|min:1',
            'status' => ['required', Rule::in(GeneralStatusEnum::getList())],
            'display_in' => 'in:1,2',   // required after front finish
            'title' => 'nullable|array',
            'title.*' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.*' => 'required|string',
            'redirect_url' => 'nullable|array',
            'redirect_url.*' => 'required|url',
            'alt_name' => 'nullable|array',
            'alt_name.*' => 'required|string|max:255',
            'images' => $imageRules,
            'images.*' => ['required_with:images','url', new CloudinaryUrlValidatorRule()],
            // 'images.*' => 'required_with:images|image|max:1048576',
            'brand_id' => ['nullable', Rule::exists('brands', 'id')]
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
