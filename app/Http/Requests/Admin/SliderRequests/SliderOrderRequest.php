<?php

namespace App\Http\Requests\Admin\SliderRequests;

use App\Enums\GeneralStatusEnum;
use App\Rules\UniqueArray;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SliderOrderRequest extends FormRequest
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
        return [
            'orders' => ['required', new UniqueArray()],
            'display_in' => 'in:1,2',   // required after front finish
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
