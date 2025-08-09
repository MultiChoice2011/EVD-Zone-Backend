<?php
namespace App\Services\Seller;
use App\Repositories\Seller\WalletRepository;
class WalletService
{
    public function __construct(public WalletRepository $walletRepository){}
    public function balanceRecharge($request)
    {
        return $this->walletRepository->balanceRecharge($request);
    }
    public function getBalanceList()
    {
        return $this->walletRepository->getBalanceList();
    }
}
