<?php

namespace App\Http\Requests\Admin\FillSerialRequests;

use App\Enums\ProductSerialType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StatusInvoiceSerialRequest extends FormRequest
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
            'invoice_id' => ['required', Rule::exists('invoices', 'id')],
            'status' => ['required', Rule::in([ProductSerialType::getTypeFree(), ProductSerialType::getTypeStopped(), ProductSerialType::getTypeRefused()])],
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
