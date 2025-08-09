<?php

namespace App\Services\Admin;

use App\Enums\OrderProductStatus;
use App\Enums\OrderProductType;
use App\Enums\WalletStatus;
use App\Repositories\Admin\OrderRepository;
use App\Repositories\Admin\ProductRepository;
use App\Repositories\Admin\SellerRepository;
use App\Repositories\Admin\StaticPageRepository;
use App\Repositories\Admin\WalletRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeService
{
    use ApiResponseAble;

    public function __construct(
        private ProductRepository           $productRepository,
        private WalletRepository            $walletRepository,
        private OrderRepository             $orderRepository,
        private SellerRepository            $sellerRepository
    )
    {}

    public function index($request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::guard('adminApi')->user();
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar,
            ];
            $data = [];
            $data['user'] = $userData;
            $data['orders_count_last_day'] = $this->orderRepository->ordersCount();
            $data['orders_total_last_day'] = $this->orderRepository->ordersTotalLastDay();
            $data['sellers_count'] = $this->sellerRepository->getTotalSeller();
            $data['waiting_orders_count'] = $this->orderRepository->waitingOrdersCount();
            $request->query->set('page', $request->input('topup_orders_page', 1));
            $request->query->set('status', OrderProductStatus::getTypeWaiting());
            $data['topup_orders'] = $this->orderRepository->getAllOrders($request, OrderProductType::getTypeTopUp());
            $request->query->set('page', $request->input('wallet_transactions_page', 1));
            $walletTransactionsStatus = in_array($request->input('wallet_transactions_status', WalletStatus::getStatusPending()), WalletStatus::getList())
                ? $request->input('wallet_transactions_status', WalletStatus::getStatusPending())
                : WalletStatus::getStatusPending();
            $request->query->set('status', $walletTransactionsStatus);
            $data['wallet_transactions'] = $this->walletRepository->getTransactions($request);
            $request->query->set('page', $request->input('stock_almost_out_page', 1));
            $data['stock_almost_out'] = $this->productRepository->stockAlmostOut($request);
            $data['wallet_transactions']->setCollection(
                $data['wallet_transactions']->getCollection()->transform(function ($transaction) {
                    $transaction->receipt_image = $transaction->ReceiptImageUrl;
                    return $transaction;
                })
            );
            DB::commit();
            return $this->ApiSuccessResponse($data, 'Home Data...!');
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }




}
