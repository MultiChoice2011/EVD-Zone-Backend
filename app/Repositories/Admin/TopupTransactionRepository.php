<?php

namespace App\Repositories\Admin;

use App\Enums\OrderStatus;
use App\Models\TopupTransaction;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class TopupTransactionRepository extends BaseRepository
{
    use ApiResponseAble;

    public function __construct(Application $app){
        parent::__construct($app);
    }


    public function store($topUpDetails, $vendorId, $orderId)
    {
        $data = [];
        foreach ($topUpDetails['topupTransaction'] as $topup){
            $data[] = [
                'vendor_id' => $vendorId,
                'order_id' => $orderId,
                'account_id' => $topup['account_id'],
                'transaction_id' => $topup['transaction_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        return $this->model->insert($data);
    }




    public function model(): string
    {
        return TopupTransaction::class;
    }
}
