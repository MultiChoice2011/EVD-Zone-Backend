<?php

namespace App\Repositories\Seller;

use App\Enums\OrderProductStatus;
use App\Enums\OrderProductType;
use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Helpers\FileUpload;
use App\Models\Customer;
use App\Models\OrderHistory;
use App\Models\OrderProduct;
use App\Models\OrderUser;
use App\Models\Product;
use App\Models\VendorProduct;
use App\Traits\ApiResponseAble;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;

class OrderUserRepository extends BaseRepository
{
    public function __construct(
        Application                          $app,
    )
    {
        parent::__construct($app);
    }


    public function showByOrderId($orderId)
    {
        return $this->model->where('order_id', $orderId)->first();
    }

    public function checkByOrderIdAndUserId($orderId, $userId)
    {
        return $this->model
            ->where('order_id', $orderId)
            ->where('user_id', $userId)
            ->first();
    }

    public function store($orderId, $userId)
    {
        return $this->model->create([
            'order_id' => $orderId,
            'user_id' => $userId,
        ]);
    }


    public function model(): string
    {
        return OrderUser::class;
    }
}
