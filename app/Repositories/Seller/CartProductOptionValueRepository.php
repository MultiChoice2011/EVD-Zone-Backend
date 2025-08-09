<?php

namespace App\Repositories\Seller;

use App\Models\CartProductOption;
use App\Models\CartProductOptionValue;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class CartProductOptionValueRepository extends BaseRepository
{
    public function __construct(
        Application $app
    )
    {
        parent::__construct($app);
    }

    public function store($cartProductId, $cartProductOptionId, $optionValueIds)
    {
        $insertArray = [];
        foreach ($optionValueIds as $id) {
            $insertArray[] = [
                'cart_product_id' => $cartProductId,
                'cart_product_option_id' => $cartProductOptionId,
                'option_value_id' => $id,
            ];
        }

        return $this->model->insert($insertArray);

    }




    public function model(): string
    {
        return CartProductOptionValue::class;
    }
}
