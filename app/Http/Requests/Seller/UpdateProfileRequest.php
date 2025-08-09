<?php

namespace App\Http\Requests\Seller;

use App\Rules\CloudinaryUrlValidatorRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone;

class UpdateProfileRequest extends FormRequest
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
        $id = 0;

        if ($this->route('id'))
            $id = $this->route('id');
        return [
            'name' => 'required|string',
            'owner_name' => ['required','string','unique:sellers,owner_name,'.auth('sellerApi')->user()->id],
            'email' => ['nullable','email','string','unique:sellers,email,'.auth('sellerApi')->user()->id],
            'phone' => [
                'nullable',
                'string',
                (new Phone)->country(config('services.laravel_phone.countries')),
                'unique:sellers,phone,'.auth('sellerApi')->user()->id
            ],
            // 'logo' => 'nullable|image|mimes:png,jpg|max:2048',
            'logo' => ['nullable','url', new CloudinaryUrlValidatorRule()],
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'region_id' => 'nullable|exists:regions,id',
            'street' => 'nullable|string|max:255',
            'commercial_register_number' => 'required',
            'tax_card_number' => 'required',
            'address' => 'required|string',
            'identity' => ['nullable','url', new CloudinaryUrlValidatorRule()],
            'commercial_register' => ['nullable','url', new CloudinaryUrlValidatorRule()],
            'tax_card' => ['nullable','url', new CloudinaryUrlValidatorRule()],
            // 'identity' => 'nullable|mimes:jpg,png,pdf,docx|max:2048',
            // 'commercial_register' => 'nullable|mimes:jpg,png,pdf,docx|max:2048',
            // 'tax_card' => 'nullable|mimes:jpg,png,pdf,docx|max:2048',
            // 'more' => 'nullable|mimes:jpg,png,pdf,docx|max:2048',
        ];
    }
}
