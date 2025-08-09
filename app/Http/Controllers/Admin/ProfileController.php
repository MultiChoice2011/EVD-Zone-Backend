<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{

    public function __construct(private ProfileService $profileService)
    {}

    public function index(Request $request)
    {
        return $this->profileService->index($request);
    }




}
