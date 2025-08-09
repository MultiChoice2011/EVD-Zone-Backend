<?php

namespace App\Http\Requests\Admin\NotificationTokenRequests;

use Illuminate\Foundation\Http\FormRequest;

class FirebaseStoreRequest extends FormRequest
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
            'firebase_token' => 'required|string'
        ];
    }

//    public function messages()
//    {
//        return [
//            '*.required' => trans("admin.general_validation.required"),
//        ];
//    }
}
