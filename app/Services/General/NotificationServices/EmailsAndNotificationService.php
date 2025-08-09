<?php

namespace App\Services\General\NotificationServices;

use App\Models\NotificationSetting;
use App\Notifications\OrderCreatedNotification;
use App\Repositories\Admin\AdminRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Throwable;

class EmailsAndNotificationService
{
    public function __construct(private FirebaseService $firebaseService, private AdminRepository $adminRepository)
    {}

    public function sendSellerNotifications($authSeller, $requestData): void
    {
        // __('notifications.order_created.title'), __('notifications.order_created.body'));
        Notification::send($authSeller, new $requestData['notificationClass']( $requestData ));
        $this->firebaseService->sendNotification(
            'seller-'.$authSeller->id,
            __('notifications.'.$requestData['notification_translations'].'.title'),
            __('notifications.'.$requestData['notification_translations'].'.body')
        );
    }

    public function sendAdminNotifications($requestData): void
    {
        if ($requestData['notification_permission_name']) {
            $admins = $this->adminRepository->getAdminsByPermissions($requestData['notification_permission_name']);
            if ($admins) {
                Notification::send($admins, new $requestData['notificationClass']( $requestData ));
                foreach ($admins as $admin) {
                    $this->firebaseService->sendNotification(
                        'admin-'.$admin->id,
                        __('notifications.'.$requestData['notification_translations'].'.title',['id' => $requestData['type_id']]),
                        __('notifications.'.$requestData['notification_translations'].'.body',['id' => $requestData['type_id']])
                    );
                }
            }
        }
    }


    public function sendEmails($user, $requestData): array
    {
        $data = ['error' => null];
        try {
            Mail::to($user->email)->send(new $requestData['emailClass']( $requestData['emailData']) );
            return $data;
        } catch (Throwable $th) {
            Log::info($th);
            $data['error'] = __('validation.email',["attribute"=> __('validation.attributes.email')]);
            return $data;
        } catch (Exception $ex) {
            $data['error'] = __('validation.email',["attribute"=> __('validation.attributes.email')]);
            return $data;
        }
    }

    public function sendAdminEmails($requestData): void
    {
        if ($requestData['notification_permission_name']) {
            $admins = $this->adminRepository->getAdminsByPermissions($requestData['notification_permission_name']);
            if ($admins) {
                foreach ($admins as $admin) {
                    try {
                        Mail::to($admin->email)->send(new $requestData['emailClass']( $requestData['emailData']) );
                    } catch (Throwable $th) {
                        Log::info($th);
                        continue;
                    } catch (Exception $ex) {
                        continue;
                    }
                }
            }
        }

    }







    // public function sendEmailsAndNotifications($authCustomer, $requestData)
    // {
    //     // $notificationSetting = isset($requestData['key']) ? NotificationSetting::where('key', $requestData['key'])->first() : null;    // 'new_orders'
    //     // first check for send Emails
    //     // if ($notificationSetting && $notificationSetting->notification_email == 1 && $authCustomer->email) {
    //         //     $data['email']['error'] = $this->sendEmails($authCustomer, $requestData);
    //     // }else{
    //     // }
    //     // second check for send Notifications for customer
    //     // if ($notificationSetting && $notificationSetting->notification_app == 1) {
    //     //     $this->firebaseService->sendNotification('customer-'.$authCustomer->id, __('notifications.order_created.title'), __('notifications.order_created.body'));
    //     //     Notification::send($authCustomer, new OrderCreatedNotification());
    //     // }
    //     // third send Notifications for admin
    //     // $admins = $this->adminRepository->getAdminsByPermissions('notifications-new-orders');
    //     // Notification::send($admins, new OrderCreatedNotification());
    // }



}
