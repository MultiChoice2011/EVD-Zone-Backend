<?php

namespace App\Repositories\Seller;

use App\Models\CartProductOption;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class CartProductOptionRepository extends BaseRepository
{
    public function __construct(
        Application $app,
        private CartProductOptionValueRepository $cartProductOptionValueRepository,
        private OptionValueRepository $optionValueRepository
    )
    {
        parent::__construct($app);
    }

    public function store($requestData, $product, $cartProductId)
    {
        $insertArray = [];
        foreach ($product->product_options as $productOption){
            // prepare data
            $data = [
                'cart_product_id' => $cartProductId,
                'product_option_id' => $productOption->id,
                'value' => null,
            ];
            // check this product_option required if true must exist in request
            $optionExist = null;
            foreach ($requestData['product_options'] as $requestOption){
                if ($requestOption['id'] == $productOption->id){
                    $optionExist = $requestOption;
                    break;
                }
            }
            if (!$optionExist)
                return false;
            if ($productOption->required == 1 && !$optionExist)
                return false;
            // check if option have option_values like select-box to take option_value_ids
            // if not have i will take value from request

            $optionValueIds = [];
            if (count($productOption->option->option_values) > 0 && isset($optionExist['option_value_ids'])){
                $optionValueIds = $this->optionValueRepository->optionValueIds($optionExist['option_value_ids'], $productOption->option->id);
                if (count($optionValueIds) == 0)
                    return false;
                //$this->cartProductOptionValueRepository->store();
                //$data['option_value_id'] = $optionExist['option_value_id'];
            }elseif(count($productOption->option->option_values) == 0 && isset($optionExist['value'])){
                $data['value'] = $optionExist['value'];
            }else{
                return false;
            }

            // Check for existing cart_product_options
            $cartProductOption = $this->model
                ->where('cart_product_id', $cartProductId)
                ->where('product_option_id', $productOption->id)
                ->first();

            if ($cartProductOption)
                $cartProductOption->update($data);
            else
                $cartProductOption = $this->model->create($data);

            // check if $optionValueIds exist to store it in new table ( cart_product_option_values )
            if (count($optionValueIds) > 0)
                $this->cartProductOptionValueRepository->store($cartProductId, $cartProductOption->id, $optionValueIds);

            $insertArray[] = $cartProductOption;
        }
        return $insertArray;
    }


    public function deleteOptions($cartProductId)
    {
        return $this->model->where('cart_product_id', $cartProductId)->delete();
    }


    public function model(): string
    {
        return CartProductOption::class;
    }
}
