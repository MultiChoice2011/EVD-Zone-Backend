<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BankCommissionSettingRequest;
use App\Services\Admin\BankCommissionService;
use Illuminate\Http\Request;

class BankCommissionController extends Controller
{
    public function __construct(public BankCommissionService $bankCommissionService){}
    public function index()
    {
        return $this->bankCommissionService->index();
    }
    public function setSetting(BankCommissionSettingRequest $request)
    {
        return $this->bankCommissionService->setSetting($request);
    }
}
