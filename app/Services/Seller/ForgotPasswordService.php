<?php
namespace App\Services\Seller;

use App\Traits\ApiResponseAble;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Events\OtpRequested;

class ForgotPasswordService{
    use ApiResponseAble;
    public function sendResetLinkEmail($request)
    {
        // Generate otp
        $otp = random_int(100000, 999999);
        // Store otp in seller table
        $this->getModel()::where('email',$request['email'])->update(
            ['otp' => $otp]
        );
        // Send email with reset link (or otp)
        try {
            event(new OtpRequested($request['email'],$otp));
            Log::info('OTP sent to seller with email ' . $request['email']);
            return $this->ApiSuccessResponse($request['email'],trans('seller.auth.password_reset'));
        } catch (\Exception $e) {
            Log::error('Failed to send OTP to seller with ID ' . $request['email'] . ': ' . $e->getMessage());
            return $this->ApiErrorResponse($e->getMessage(),trans('admin.general_error'));
        }
    }
    public function resetPassword($request)
    {
        try{
            DB::beginTransaction();
            // Check if token is valid
            $reset = $this->getModel()::whereEmail($request['email'])
            ->whereOtp($request['otp'])
            ->first();
            if(!$reset)
                return $this->ApiErrorResponse('','Invalid otp or email');
            // otp is valid, update the password
            //get seller by email
            $user = $this->getModelByEmail($request['email']);
            // update password
            $user->password = Hash::make($request['password']);
            $user->save();
            // update otp to null
            $this->getModel()::where('email', $request['email'])->update(['otp' => null]);
            DB::commit();
            return $this->ApiSuccessResponse([],trans('seller.auth.reset_password_msg_success'));
        }catch(\Exception $ex){
            DB::rollBack();
            return $this->ApiErrorResponse($ex->getMessage(),trans('admin.general_error'));
        }
    }
    private function getModelByEmail($email)
    {
        return $this->getModel()::where('email',$email)->first();
    }
    private function getModel()
    {
        return '\App\Models\Seller';
    }
}
