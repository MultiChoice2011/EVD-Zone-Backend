<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequests\MainSettingRequest;
use App\Http\Requests\Admin\SettingRequests\NotificationSettingRequest;
use App\Services\Admin\NotificationSettingService;
use App\Services\Admin\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationSettingController extends Controller
{

    public function __construct(private NotificationSettingService $notificationSettingService)
    {}

    public function getNotificationSettings()
    {
        return $this->notificationSettingService->getNotificationSettings();
    }

    public function updateNotificationSettings($id, NotificationSettingRequest $request)
    {
        return $this->notificationSettingService->updateNotificationSettings($id, $request);
    }


}
