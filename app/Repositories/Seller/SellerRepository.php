<?php

namespace App\Repositories\Seller;

use App\Enums\SellerApprovalType;
use App\Models\Order;
use App\Models\Seller;
use App\Models\Wallet;
use App\Services\General\CurrencyService;
use App\Services\Seller\SellerService;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class SellerRepository extends BaseRepository
{
    public function __construct(
        Application $app,
        private BalanceHistoryRepository        $balanceHistoryRepository,
        private SellerAddressRepository         $sellerAddressRepository,
        private SellerAttachmentRepository      $sellerAttachmentRepository,
        private SellerTranslationRepository     $sellerTranslationRepository,
    )
    {
        parent::__construct($app);
    }

    public function store($requestData)
    {
        $seller = $this->model->create([
            'name' => $requestData['name'],
            'owner_name' => $requestData['owner_name'],
            'email' => $requestData['email'],
            'phone' => $requestData['phone'],
            'currency_id' => $requestData['currency_id'],
//            'password' => bcrypt($requestData['password']),
//            'google2fa_secret' => $requestData->google2fa_secret,
        ]);
        $seller->sellerAddress()->create([
            'seller_id' => $seller->id,
            'country_id' => $requestData['country_id'],
            'city_id' => $requestData['city_id'],
        ]);
        return $seller;
    }


    public function show($sellerId)
    {
        // get one seller
        $seller = $this->model->where('id', $sellerId)
            ->with([
                'admin:id,name,email',
                'sellerGroup',
                'sellerGroupLevel',
                'children',
                'sellerAddress.country',
                'sellerAddress.city',
                'sellerAddress.region',
                'sellerAttachment',
                'seller_transactions',
            ])
            ->withCount('children as sellers_count')
            ->first();

        return $seller;
    }
    public function showByPhone($phone)
    {
        return $this->model->where('phone', $phone)->first();
    }
    public function makeVerify(Seller $seller)
    {
        $seller->verify = 1;
        $seller->save();
        return true;
    }

    public function decreaseBalance(Seller $seller, Order $order)
    {
        // Get the current balance of the seller
        $balanceBefore = $seller->balance;

        // Calculate the balance after subtracting the order total
        $orderTotal = number_format(($order->total), 4, '.', '');
        // get current currency for auth seller
        $currency = CurrencyService::getCurrentCurrency($seller);
        $conversionRate = $currency->value ?? 1;
        // return conversion back again to default currency with value 1
        $balanceAfter = ($balanceBefore - $orderTotal) / $conversionRate;
        Log::info('orderTotal: ' . $orderTotal);
        Log::info('balanceAfter: ' . $balanceAfter);

        // Ensure balance doesn't go negative, handle as needed
        if ($balanceAfter < 0) {
            return false;
        }

        // Update the seller's balance
        $seller->balance = $balanceAfter;
        $seller->save();
        Log::info('finished'.$seller->balance);
        // Create a balance history record
        $balanceHistoryData = [
            'order_id' => $order->id,
            'balance_before' => ($balanceBefore / $conversionRate),
            'balance_after' => $balanceAfter,
            'ownerble_id' => $seller->id,
            'ownerble_type' => get_class($seller),
        ];
        $this->balanceHistoryRepository->store($balanceHistoryData);

        return true;
    }

    public function changeApprovalStatus(Seller $seller, $status = SellerApprovalType::PENDING): Seller
    {
        $seller->approval_status = $status;
        $seller->save();
        return $seller;
    }

    public function model(): string
    {
        return Seller::class;
    }
}
