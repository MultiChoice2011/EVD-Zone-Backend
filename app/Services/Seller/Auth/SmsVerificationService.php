<?php

namespace App\Services\Seller\Auth;

//use App\Enums\CustomerDeviceType;
//use App\Enums\WhatsappTempEnum;
//use App\Jobs\SendWhatsAppMessage;
use App\Enums\CustomerDeviceType;
use App\Jobs\SendWhatsAppMessage;
use App\Models\Seller;
use App\Repositories\Admin\IntegrationRepository;
use App\Repositories\Seller\CodeVerificationRepository;
use App\Repositories\Seller\SellerSessionRepository;
use App\Repositories\Seller\SellerRepository;
use App\Repositories\Seller\SettingRepository;
use App\Services\General\SmsVerification\SmsVerificationServiceFactory;
use App\Services\General\WhatsappIntegration\WhatsappService;
use App\Services\BaseService;
use App\Traits\ApiResponseAble;
use App\Traits\LoggingTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\PhoneNumber;
use Tymon\JWTAuth\Facades\JWTAuth;

class SmsVerificationService extends BaseService
{


    private $smsVerificationService;
    private $otpSixDigit;
    public function __construct(
        protected Container                         $container,
        protected IntegrationRepository             $integrationRepository,
        protected CodeVerificationRepository        $codeVerificationRepository,
        protected SellerRepository                $sellerRepository,
        protected SellerSessionRepository         $sellerSessionRepository,
        protected SettingRepository                 $settingRepository,
        protected WhatsappService $whatsappService,
    )
    {
        // get type of sms verification type from setting
        $service = $this->settingRepository->getSettingByKeyword('sms_verification_type');
        $integration = $this->integrationRepository->showByName($service ?? 'msegat');
        // get service
        $this->smsVerificationService = SmsVerificationServiceFactory::create($integration);
        // get setting otp type
        $this->otpSixDigit = $this->settingRepository->getSettingByKeyword('otp_6_digit');
    }
    public function resendOtp($request)
    {
        try {
            DB::beginTransaction();
            // Get Customer by phone
            $customer = $this->sellerRepository->showByPhone($request->phone);
            if (! $customer)
                return $this->ApiErrorResponse(null, 'This phone not valid');
            // Check if is already verified
            if ($customer->verify == 1)
                return $this->ApiSuccessResponse(null, "This customer already verified");
            // Send OTP to customer phone
            $message = $this->sendOtp($customer);
            if (! $message)
                return $this->ApiErrorResponse();
            // success of prev. processes
            DB::commit();
            return $this->ApiSuccessResponse(null, "OTP resent Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse([], $e->getMessage());
//            return $this->ApiErrorResponse($this->logException($e), __('admin.general_error'));
        }
    }

    public function verifyOtp($request)
    {
        try {
            DB::beginTransaction();
            // Get Customer by phone
            $formatWithoutZero = (new PhoneNumber('+'. $request->phone))->formatE164();
            $formatWithoutZero = str_replace('+', '', $formatWithoutZero);
            $customer = $this->sellerRepository->showByPhone($request->phone);
            if (! $customer){
                $customer = $this->sellerRepository->showByPhone($formatWithoutZero);
            }
            if (! $customer){
                return $this->ApiErrorResponse(null, 'this phone not valid');
            }
            // Get verification code
            $request->verifiable_type = Seller::class;
            $request->verifiable_id = $customer->id;
            $verification = $this->codeVerificationRepository->getByCustomerId($request);
            // Verify OTP and Update customer row
            if ($verification) {
                // use verify api if code store as id
                if ($verification->is_id == 1){
                    $verifyData = [];
                    $verifyData['id'] = $verification->code;
                    $verifyData['code'] = $request->otp;
                    // Make verification with api
                    $message = $this->smsVerificationService->verifyOtp($verifyData);
                    // check if api success
                    if (! $message)
                        return $this->ApiErrorResponse();
                    // Now you can change customer verify
                    $customerVerified = $this->sellerRepository->makeVerify($customer);
                    $verificationUsed = $this->codeVerificationRepository->updateUsed($verification);
                    if (!$customerVerified || !$verificationUsed)
                        return $this->ApiErrorResponse();
                }elseif ($verification->code == $request->otp){
                    // if is same otp now you can make customer verify
                    $customerVerified = $this->sellerRepository->makeVerify($customer);
                    $verificationUsed = $this->codeVerificationRepository->updateUsed($verification);
                    if (!$customerVerified || !$verificationUsed)
                        return $this->ApiErrorResponse();
                }else
                    // Anything else return Expire
                    return $this->ApiErrorResponse(null, 'Token expire or not valid, please try again later');
            }else
                return $this->ApiErrorResponse(null, 'Token expire or not valid, please try again later');
            // Generate token for customer
            $token = $this->generateToken($customer);
            // Get authenticated customer
            $customer = auth('sellerApi')->user();
            // Invalidate previous token and mark session as expired
            $this->invalidatePreviousSession($customer);
            // Save the new session
            $sellerSessionData = [
                'seller_id' => $customer->id,
                'device_type' => CustomerDeviceType::getTypeWeb(),
                'device_id' => $request->device_id ?? null,
                'token' => $token
            ];
            $this->sellerSessionRepository->store($sellerSessionData);
            // success of prev. processes
            DB::commit();
            return $this->ApiSuccessResponse(['token' => $token], "Verified Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse([], $e->getMessage());
//            return $this->ApiErrorResponse($this->logException($e), __('admin.general_error'));
        }
    }
    public function sendOtp(Seller $seller, $otpToken = 1)
    {
        // check if send otp with four or six digits
        if ($this->otpSixDigit){
            // send verification sms with 6 digit
            $message = $this->smsVerificationService->sendSixDigitOtp($seller->phone);
        }else{
            // send verification sms with 4 digit
            $message = $this->smsVerificationService->sendFourDigitOtp($seller->phone);
        }
        // Store code in db
        $message['verifiable_type'] = Seller::class;
        $message['verifiable_id'] = $seller->id;
        $message['type'] = 'phone';
        $message['token'] = $otpToken ? Str::random(40) : null;
        $message['expire_at'] = Carbon::now()->addMinutes(3);
        $this->codeVerificationRepository->store($message);
        if (config('services.whatsapp.messages_otp', false)){
            $this->whatsappService->sendMessage($seller->phone,$message['code'] . ' ' . trans("whatsapp.login_code"));
        }
        return true;
    }


    private function generateToken($customer)
    {
        // Generate new token
        //$token = Auth::guard("customerApi")->attempt($credentials);
        $token = Auth::guard("sellerApi")->login($customer);
        return $token;
    }

    private function invalidatePreviousSession($customer)
    {
        // Find the last session with an active token for this customer
        $previousSession = $this->sellerSessionRepository->lastSession($customer->id);

        // If there is an active session, invalidate the previous token
        if ($previousSession && $previousSession->token) {
            try {
                JWTAuth::setToken($previousSession->token)->invalidate();
            } catch (Exception $e) {
                $this->logException($e);
                Log::info($previousSession->token);
            }

            // Mark session as expired
            $previousSession->update(['expired_at' => now()]);
        }
    }



}
