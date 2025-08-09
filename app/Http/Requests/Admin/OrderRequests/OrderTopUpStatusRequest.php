<?php

namespace App\Http\Requests\Admin\OrderRequests;

use App\Enums\OrderProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderTopUpStatusRequest extends FormRequest
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
            'status' => ['required', Rule::in([OrderProductStatus::getTypeCompleted()])],
        ];
    }

    public function messages()
    {
        return [
            '*.required' => trans("admin.general_validation.required"),
        ];
    }
}
