<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\BalanceRechargeRequest;
use App\Services\Seller\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(public WalletService $walletService){}
    public function balanceRecharge(BalanceRechargeRequest $request)
    {
        return $this->walletService->balanceRecharge($request->validated());
    }
    public function getBalanceList()
    {
        return $this->walletService->getBalanceList();
    }
}
