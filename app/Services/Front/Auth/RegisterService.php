<?php

namespace App\Services\Front\Auth;

use App\Repositories\Admin\SellerRepository;
use App\Services\General\NotificationServices\EmailsAndNotificationService;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Log;


class RegisterService
{
    use ApiResponseAble;

    public function __construct(
        protected Container                         $container,
        protected SellerRepository                  $sellerRepository,
        // protected CodeVerificationRepository        $codeVerificationRepository,
        // protected SmsVerificationService            $smsVerificationService,
        // protected SettingRepository                 $settingRepository,
        // protected EmailsAndNotificationService      $emailsAndNotificationService,
    )
    {}


    public function generateG2FAuth()
    {
        try {
            DB::beginTransaction();
            $data = [];
            $google2fa = new Google2FA();
            $data['secretKey'] = $google2fa->generateSecretKey();
            $data['qrCodeUrl'] = $google2fa->getQRCodeUrl(
                config('app.name'),
                '2FAuth',
                $data['secretKey']
            );
            
            DB::commit();
            return $this->ApiSuccessResponse($data, "Generated Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    public function register($request)
    {
        try {
            DB::beginTransaction();
            $seller = $this->sellerRepository->store($request);
            if (! $seller)
                return $this->ApiErrorResponse();
            // Send OTP to customer phone
            $message = $this->smsVerificationService->sendOtp($seller);
            if (! $message)
                return $this->ApiErrorResponse();

            // // make notifications ( as cronjob later )
            // $requestData = [
            //     'notification_permission_name' => 'notifications-new-customers',
            //     'notificationClass' => CustomNotification::class,
            //     'notification_translations' => 'admin_customer_created',
            //     'type' => 'customer',
            //     'type_id' => $customer->id,
            //     //////////////
            //     'emailClass' => AdminCustomerCreatedEmail::class,
            //     'emailData' => ['test' => null],
            // ];
            // $this->emailsAndNotificationService->sendAdminNotifications($requestData);
            // $this->emailsAndNotificationService->sendAdminEmails($requestData);

            DB::commit();
            return $this->ApiSuccessResponse($data, "Registered Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }



}
