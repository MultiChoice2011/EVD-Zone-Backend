<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\Seller\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(public SettingService $settingService){}
    public function mainSetting()
    {
        return $this->settingService->mainSettings();
    }
}
