<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BankCommissionSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bank_commission_id' => 'required|integer|exists:bank_commissions,id',
            'settings' => 'required|array|min:1',
            'settings.*.name' => 'required|string|in:MADA,VISA,MASTER,STC_PAY,APPLEPAY',
            'settings.*.gate_fees' => 'required|integer',
            'settings.*.static_value' => 'required|integer',
            'settings.*.additional_value_fees' => 'required|integer',
        ];
    }
}
