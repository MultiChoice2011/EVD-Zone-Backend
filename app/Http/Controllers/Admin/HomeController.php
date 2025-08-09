<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequests\StaticPageRequest;
use App\Http\Requests\Admin\SettingRequests\StaticPageStatusRequest;
use App\Services\Admin\HomeService;
use App\Services\Admin\SettingService;
use App\Services\Admin\StaticPageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    public function __construct(private HomeService $homeService)
    {}

    public function index(Request $request)
    {
        return $this->homeService->index($request);
    }

   


}
