<?php
namespace App\Services\Seller;

use App\Enums\GeneralStatusEnum;
use App\Events\SellerRegisterd;
use App\Helpers\FileUpload;
use App\Http\Resources\Seller\AuthResource;
use App\Http\Resources\Seller\RegisterResource;
use App\Http\Resources\Seller\SellerRejectReasonResource;
use App\Http\Resources\Seller\SellerResource;
use App\Mail\AdminOrderCreatedEmail;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Permission;
use App\Models\Seller;
use App\Models\SellerAttachment;
use App\Notifications\CustomNotification;
use App\Repositories\Seller\AuthRepository;
use App\Repositories\Seller\CountryRepository;
use App\Repositories\Seller\CurrencyRepository;
use App\Repositories\Seller\SellerRejectReasonRepository;
use App\Repositories\Seller\SellerRepository;
use App\Repositories\Seller\SettingRepository;
use App\Services\General\CurrencyService;
use App\Services\General\NotificationServices\EmailsAndNotificationService;
use App\Services\Seller\Auth\SmsVerificationService;
use App\Traits\ApiResponseAble;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use PragmaRX\Google2FA\Google2FA;
use Propaganistas\LaravelPhone\PhoneNumber;
use App\Services\Seller\Helpers\SellerHelperService;
class AuthService{
    use ApiResponseAble,FileUpload;
    public function __construct(
        public AuthRepository                $authRepository,
        public SellerRepository              $sellerRepository,
        public SellerRejectReasonRepository  $sellerRejectReasonRepository,
        private EmailsAndNotificationService $emailsAndNotificationService,
        private SmsVerificationService       $smsVerificationService,
        private SettingRepository            $settingRepository,
        private CountryRepository            $countryRepository,
        private SellerHelperService          $sellerHelperService,
    ){}
    public function register($request)
    {
        try{
            // get default currency for seller created
            $currency = $this->sellerHelperService->getDefaultSellerCurrency($request['country_id']);
            $request['currency_id'] = $currency->id;

            $permissions = Permission::where('guard_name','sellerApi')->get();
            $seller = $this->sellerRepository->store($request);
            #assign role super seller for seller user
            $seller->assignRole('Super Seller');
            #assign permission to seller user
            $seller->givePermissionTo($permissions);
            if (!$seller)
                return $this->ApiErrorResponse();
            // Send OTP to customer phone
            $message = $this->smsVerificationService->sendOtp($seller);
            if (!$message)
                return $this->ApiErrorResponse();
            return $this->ApiSuccessResponse(RegisterResource::make($request),'success message');
        }catch (\Exception $exception){
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }

    public function login($request)
    {
        try{
            DB::beginTransaction();
            // Handle formate of phone number
            $request->phone = (new PhoneNumber('+'. $request->phone))->formatE164();
            $request->phone = str_replace('+', '', $request->phone);
            // Check first if this phone exist
            $seller = $this->sellerRepository->showByPhone($request->phone);
            if(! $seller)
                return $this->ApiErrorResponse(null, 'You are not registered...');
            // Check if this customer is blocked or not
            if($seller->status == GeneralStatusEnum::getStatusInactive())
                return $this->ApiErrorResponse(null, 'You are blocked, please return to support.');
            // Send OTP to customer phone
            $message = $this->smsVerificationService->sendOtp($seller);
            if (! $message)
                return $this->ApiErrorResponse();
            DB::commit();
            return $this->ApiSuccessResponse(RegisterResource::make($seller), "Need OTP");
        }catch(\Exception $ex){
            DB::rollBack();
            return $this->ApiErrorResponse($ex->getMessage(),__('admin.general_error'));
        }
    }
    public function profile()
    {
        try{
            $seller = auth('sellerApi')->user();
            $seller->load(['rejectReasons' => function ($query) {
                $query->unresolved();
            }]);
            if(! $seller)
                return $this->ApiErrorResponse();

            return $this->ApiSuccessResponse(AuthResource::make($seller));
        }catch(\Exception $ex){
            return $this->ApiErrorResponse($ex->getMessage(),__('admin.general_error'));
        }
    }
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->ApiSuccessResponse([],trans('seller.auth.logout_message'));
    }
    public function verifyG2FAuth($request)
    {
        try{
            // Verify the OTP
            $google2fa = new Google2FA();
            $valid = $google2fa->verifyKey($request->google2fa_secret, $request->otp);
            if (!$valid) {
                return response()->json(['error' => 'Invalid OTP code.'], 422);
            }
            $permissions = Permission::where('guard_name','sellerApi')->get();
            $createSeller = $this->authRepository->createSeller($request);
            #create seller address
            $createSeller->sellerAddress()->create([
                'seller_id' => $createSeller->id,
                'country_id' => $request->country_id,
                'city_id' => $request->city_id,
            ]);
            #assign role super seller for seller user
            $createSeller->assignRole('Super Seller');
            #assign permission to seller user
            $createSeller->givePermissionTo($permissions);
            // Generate a JWT token for the user
            $token = JWTAuth::fromUser($createSeller);
            // send notification for admin
            event(new SellerRegisterd($createSeller));
            return $this->ApiSuccessResponseAndToken(AuthResource::make($createSeller),'success message',$token);
        }catch(\Exception $ex){
            return $this->ApiErrorResponse($ex->getMessage(),__('admin.general_error'));
        }
    }
    public function updateProfile($request)
    {
        try{
            DB::beginTransaction();
            $seller = auth('sellerApi')->user();
            // get e164 format and remove +
            $formatWithoutZero = (new PhoneNumber($request['phone']))->formatE164();
            $request['phone'] = str_replace('+', '', $formatWithoutZero);
            // update user info.
            $seller = $this->authRepository->updateSeller($seller,$request);
            // make rejected reasons as resolved
            $this->sellerRejectReasonRepository->makeResolved($seller->id);
            // change approval status to pending
            // $seller = $this->sellerRepository->changeApprovalStatus($seller);

            DB::commit();
            return $this->ApiSuccessResponse(SellerResource::make($seller),'update seller data success');
        }catch(\Exception $ex)
        {
            DB::rollBack();
            return $this->ApiErrorResponse($ex->getMessage(),trans('admin.general_error'));
        }
    }

}
