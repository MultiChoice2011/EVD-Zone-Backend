<?php

namespace App\Repositories\Admin;

use App\Models\ProductPriceSellerGroup;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class ProductPriceSellerGroupRepository extends BaseRepository
{


    public function __construct(Application $app)
    {
        parent::__construct($app);
    }
    public function store($requestData)
    {
        if (count($requestData['seller_group']) == 0 ) {
            return false;
        }
        foreach ($requestData['seller_group'] as $sellerGroup) {
            if (
                $sellerGroup['price'] < $requestData['cost_price']
                // $sellerGroup['price'] < $requestData['wholesale_price']
            ) {
                return false;
            }
            $this->model->create([
                'product_id' => $requestData['product_id'],
                'seller_group_id' => $sellerGroup['id'],
                'price' => $sellerGroup['price'],
            ]);
        }
        return true;
    }
    public function deleteByProductId($productId)
    {
        $this->model->where('product_id', $productId)->delete();
        return true;
    }
    public function model(): string
    {
        return ProductPriceSellerGroup::class;
    }
}
