<?php

namespace App\Repositories\Seller;

use App\Models\OrderProductOption;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class OrderProductOptionRepository extends BaseRepository
{
    public function __construct(
        Application $app,
        private OrderProductOptionValueRepository $orderProductOptionValueRepository,
        private OptionValueRepository $optionValueRepository,
    )
    {
        parent::__construct($app);
    }

    public function store($requestData, $orderProductId)
    {
        foreach ($requestData as $cartOption){
            $orderProductOption = $this->model->create([
                'order_product_id' => $orderProductId,
                'product_option_id' => $cartOption->product_option_id,
                'value' => $cartOption->value ?? null,
            ]);
            $this->orderProductOptionValueRepository->store($cartOption->cartProductOptionValues, $orderProductOption->id, $orderProductId);
        }
        return true;
    }




    public function model(): string
    {
        return OrderProductOption::class;
    }
}
