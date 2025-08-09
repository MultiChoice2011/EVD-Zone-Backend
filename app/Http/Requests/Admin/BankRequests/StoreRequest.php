<?php

namespace App\Http\Requests\Admin\BankRequests;

use App\Rules\AllLanguagesRequired;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'name' => ['required','array',new AllLanguagesRequired()],
            'name.*' => ['required','string','max:191'],
            'description' => ['required','array',new AllLanguagesRequired()],
            'description.*' => ['required','string'],
            'account_number' => ['required','string'],
            'iban_number' => ['required','string'],
            'country_id' => ['required', 'array'], // Ensure countries is an array
            'country_id.*' => ['required', 'integer', 'exists:countries,id'], // Validate each country ID
        ];
    }
}
