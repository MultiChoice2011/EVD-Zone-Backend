<?php

namespace App\Http\Requests\Admin\SellerRequests;

use App\Enums\SellerApprovalType;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SellerApprovalStatusRequest extends FormRequest
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
    public function rules(Request $request)
    {
        return [
            'approval_status' => ['required', Rule::in([SellerApprovalType::getTypeApproved(), SellerApprovalType::getTypeRejected()])],
            'reject_reason' => ['array', Rule::requiredIf(function() use($request){
                return $request->approval_status == SellerApprovalType::getTypeRejected();
            })],
            'reject_reason.*' => 'string'
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
