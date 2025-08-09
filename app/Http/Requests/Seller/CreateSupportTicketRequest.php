<?php

namespace App\Http\Requests\Seller;

use App\Enums\ErrorMessageEnum;
use App\Rules\CloudinaryUrlValidatorRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateSupportTicketRequest extends FormRequest
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
            'title' => 'required|string',
            'order_id' => 'required|exists:orders,id',
            'details' => 'required|string|max:300',
            "status" => "nullable|string",
            'attachments' => ['nullable','url', new CloudinaryUrlValidatorRule()],
            // 'attachments' => 'nullable|mimes:png,jpg,pdf|max:2048',
            'error_message' => 'required|string|in:' . implode(',', [
                    ErrorMessageEnum::ALREADY_USED,
                    ErrorMessageEnum::CODE_INCORRECT,
                    ErrorMessageEnum::INEFFICIENT,
                    ErrorMessageEnum::OTHER,
                ]),
        ];
    }
}
