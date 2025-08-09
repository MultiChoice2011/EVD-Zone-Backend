<?php

namespace App\Services\Front\Auth;

use App\Enums\GeneralStatusEnum;
use App\Repositories\Front\CodeVerificationRepository;
use App\Repositories\Front\CustomerRepository;
use App\Repositories\Front\FirebaseTokenRepository;
use App\Repositories\Front\SettingRepository;
use App\Services\General\NotificationServices\FirebaseService;
use App\Traits\ApiResponseAble;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use Propaganistas\LaravelPhone\PhoneNumber;


class LoginService
{
    use ApiResponseAble;

    public function __construct(
        protected Container $container,
        // protected CustomerRepository $customerRepository,
        // protected CodeVerificationRepository $codeVerificationRepository,
        // protected SmsVerificationService $smsVerificationService,
        // protected SettingRepository $settingRepository,
        // protected FirebaseTokenRepository $firebaseTokenRepository,
        protected FirebaseService $firebaseService,
    )
    {}


    public function login($request)
    {
        try {
            DB::beginTransaction();

            $otp_secret = 'ssssssssssssssss';
            $google2fa = new Google2FA();
            $one_time_password = $google2fa->getCurrentOtp($otp_secret);
            return $this->ApiSuccessResponse($one_time_password, "Login Successfully");

            // // Handle formate of phone number
            // $request->phone = (new PhoneNumber('+'. $request->phone))->formatE164();
            // $request->phone = str_replace('+', '', $request->phone);
            // // Check first if this phone exist
            // $customer = $this->customerRepository->showByPhone($request->phone);
            // if(! $customer)
            //     return $this->ApiErrorResponse(null, 'You are not registered...');
            // // Check if password is correct
            // Log::info($customer->password);
            // if (! Hash::check($request->password, $customer->password))
            //     return $this->ApiErrorResponse(null, 'Email or Password incorrect.');
            // // Check if this customer is blocked or not
            // if($customer->status == GeneralStatusEnum::getStatusInactive())
            //     return $this->ApiErrorResponse(null, 'You are blocked, please return to support.');
            // // Check if this customer is not verified
            // if(! $customer->verify){
            //     // Send OTP to customer phone
            //     $message = $this->smsVerificationService->sendOtp($customer);
            //     if (! $message)
            //         return $this->ApiErrorResponse();
            //     // Change next action to 1 , app must go to otp screen
            //     $response = ['token' => null, 'next_action' => 1];
            //     DB::commit();
            //     return $this->ApiSuccessResponse($response, "Need OTP");

            // }else{
            //     // Generate token by email and password if correct
            //     $credentials = ['phone' => $request->phone, 'password' => $request->password];
            //     $token = $this->generateToken($credentials);

            //     // success of prev. processes
            //     DB::commit();
            //     $response = ['token' => $token, 'next_action' => 0];
            //     return $this->ApiSuccessResponse($response, "Login Successfully");
            // }
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    // public function logout($request)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $authCustomer = Auth::guard('customerApi')->user();
    //         $firebaseTokenNullable = $this->firebaseTokenRepository->makeNullable($request, $authCustomer);
    //         $this->firebaseService->unsubscribeFromTopic($request->firebase_token, 'customer-'.$authCustomer->id);

    //         // Invalid for customer token
    //         auth('customerApi')->logout();
    //         // success of prev. processes
    //         DB::commit();
    //         return $this->ApiSuccessResponse(null, "Logout Successfully");

    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         //return $this->ApiErrorResponse(null, $e);
    //         return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
    //     }
    // }





    private function generateToken($credentials)
    {
        // Generate new token
        $token = Auth::guard('customerApi')->attempt($credentials);
        return $token;
    }


}
