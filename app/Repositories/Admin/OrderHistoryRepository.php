<?php

namespace App\Repositories\Admin;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderHistory;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class OrderHistoryRepository extends BaseRepository
{

    public function __construct(
        Application $app,
    ){
        parent::__construct($app);
    }

    public function store($orderId)
    {
        return $this->model->create([
            'order_id' => $orderId,
            'status' => OrderStatus::PENDING,
            'note' => 'first',
        ]);

    }

    public function storeOrderHistoryComplete($orderId)
    {
        return $this->model->create([
            'order_id' => $orderId,
            'status' => OrderStatus::COMPLETED,
            'note' => 'first',
        ]);

    }

    /**
     * OrderHistory Model
     *
     * @return string
     */
    public function model(): string
    {
        return OrderHistory::class;
    }
}
