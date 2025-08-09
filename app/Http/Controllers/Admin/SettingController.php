<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequests\MainSettingRequest;
use App\Http\Requests\Admin\SettingRequests\NotificationSettingRequest;
use App\Services\Admin\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{

    public function __construct(private SettingService $settingService)
    {}

    public function mainSettings()
    {
        return $this->settingService->mainSettings();
    }

    public function updateMainSettings(MainSettingRequest $request)
    {
        return $this->settingService->updateMainSettings($request);
    }



}
