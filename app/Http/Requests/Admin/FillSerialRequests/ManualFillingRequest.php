<?php

namespace App\Http\Requests\Admin\FillSerialRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManualFillingRequest extends FormRequest
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
            'vendor_id' => 'required|integer',
            'product_id' => 'required|integer',
            'source_type' => 'required|in:manual,pull,file',
            'expiring' => 'required|date_format:Y-m-d',
            'buying' => 'required|date_format:Y-m-d',
            'product_serials.*.serial' => 'required|string',
            'product_serials.*.scratching' => 'required|string',
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
