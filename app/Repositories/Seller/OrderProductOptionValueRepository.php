<?php

namespace App\Repositories\Seller;

use App\Models\OrderProductOptionValue;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class OrderProductOptionValueRepository extends BaseRepository
{
    public function __construct(
        Application $app
    )
    {
        parent::__construct($app);
    }

    public function store($cartProductOptionValues, $cartProductOptionId, $orderProductId)
    {
        $insertArray = [];
        foreach ($cartProductOptionValues as $cartOptionValue){
            $insertArray[] = [
                'order_product_id' => $orderProductId,
                'order_product_option_id' => $cartProductOptionId,
                'option_value_id' => $cartOptionValue->option_value_id,
            ];
        }
        return $this->model->insert($insertArray);
    }




    public function model(): string
    {
        return OrderProductOptionValue::class;
    }
}
