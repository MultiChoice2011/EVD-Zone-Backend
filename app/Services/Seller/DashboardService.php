<?php
namespace App\Services\Seller;
use App\Http\Resources\Seller\DashboardResource;
use App\Models\Order;
use App\Models\Wallet;
use App\Repositories\Seller\OrderRepository;
use App\Repositories\Seller\SupportTicketRepository;
use App\Repositories\Seller\WalletRepository;
use App\Traits\ApiResponseAble;

class DashboardService
{
    use ApiResponseAble;
    public function __construct(
        public OrderRepository $orderRepository,
        public WalletRepository $walletRepository,
        public SupportTicketRepository $supportTicketRepository,
    ){}
    public function index()
    {
        try{
            $data = [];
            $data['order_count'] = $this->orderRepository->getCountOfOrders('hours', 24);
            $data['total_value_of_orders'] = $this->orderRepository->getTotalValueOfOrders('hours', 24);
            $data['total_of_wallets'] = $this->walletRepository->getTotalAmountOfWallet();
            $data['wallet_transactions_cash'] = $this->walletRepository->getCashTransactions();
            $data['tickets_pending'] = $this->supportTicketRepository->getTicketsPending();
            if(!empty($data && $data['wallet_transactions_cash'] &&$data['tickets_pending'])){
                $dataObject = (Object) $data;
                return $this->ApiSuccessResponse(DashboardResource::make($dataObject));
            }
        }catch (\Exception $exception){
            return $this->ApiErrorResponse($exception->getMessage(),trans("admin.general_error"));
        }
    }
}
