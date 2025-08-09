<?php

namespace App\Http\Requests\Admin\IntegrationRequests;

use App\Enums\GeneralStatusEnum;
use App\Enums\Integration\IntegrationTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IntegrationKeysFilterRequest extends FormRequest
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
            'type' => ['nullable','string', Rule::in(IntegrationTypeEnum::getList())],
        ];
    }

}
