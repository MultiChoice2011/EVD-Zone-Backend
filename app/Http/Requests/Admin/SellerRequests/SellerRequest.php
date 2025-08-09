<?php

namespace App\Http\Requests\Admin\SellerRequests;

use App\Rules\CloudinaryUrlValidatorRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone;

class SellerRequest extends FormRequest
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
            'seller_group_id' => ['nullable', Rule::exists('seller_groups', 'id')],
            // 'seller_group_level_id' => ['nullable', Rule::exists('seller_group_levels', 'id')],
            // 'parent_id' => [Rule::exists('sellers', 'id')],
            'name' => 'required|string',
            'owner_name' => ['required', 'string', Rule::unique('sellers', 'owner_name')->ignore($id)],
            'email' => ['required', 'email', 'string', Rule::unique('sellers', 'email')->ignore($id)],
            'phone' => ['required', 'string', Rule::unique('sellers', 'phone')->ignore($id, 'id'), (new Phone)->country(config('services.laravel_phone.countries'))],
            //'password' => ['required_if:id,'.$id,'string', 'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[^\w\s]).{8,}$/'],
            'logo' => [Rule::when($this->isMethod('post'), ['nullable','url', new CloudinaryUrlValidatorRule()])],
            // 'logo' => ['image', Rule::when($this->isMethod('post'), 'image|max:1048576')],
            //'status' => 'required|in:active,inactive',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'exists:cities,id',
            'region_id' => 'exists:regions,id',
            'address_details' => 'nullable|string',
            'identity' => 'array',
            'identity.*' => ['nullable','url', new CloudinaryUrlValidatorRule()],
            // 'identity.*' => 'mimes:pdf,xlx,csv,jpeg,png,jpg|max:1048576',
            'commercial_register' => 'array',
            'commercial_register.*' => ['nullable','url', new CloudinaryUrlValidatorRule()],
            'tax_card' => 'array',
            'tax_card.*' => ['nullable','url', new CloudinaryUrlValidatorRule()],
            //'deleted_attachments' => 'array',

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
