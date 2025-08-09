<?php

namespace App\Http\Requests\Admin\IntegrationRequests;

use App\Enums\GeneralStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IntegrationRequest extends FormRequest
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
            'status' => ['required', Rule::in(GeneralStatusEnum::getList())],
            'vendor_id' => [Rule::exists('vendors', 'id')],
            // 'keys' => 'required|array',
            // 'keys.*.key' => 'required|string',
            // 'keys.*.value' => 'required|string',
        ];
    }
}
