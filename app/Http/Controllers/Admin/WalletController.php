<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\Admin\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(public WalletService $walletService){}
    public function index(Request $request)
    {
        return $this->walletService->getBalanceTransactions($request);
    }
    public function show($id)
    {
        return $this->walletService->show($id);
    }
    public function complete($id)
    {
        return $this->walletService->complete($id);
    }
    public function refused($id)
    {
        return $this->walletService->refused($id);
    }
}
