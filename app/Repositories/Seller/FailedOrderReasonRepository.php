<?php

namespace App\Repositories\Seller;

use App\Models\FailedOrderReason;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;

class FailedOrderReasonRepository extends BaseRepository
{

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }


    public function store($requestData)
    {
        return $this->model->create([
            'order_id' => $requestData['order_id'],
            'reason' => $requestData['reason'],
        ]);

    }


    public function model(): string
    {
        return FailedOrderReason::class;
    }
}
