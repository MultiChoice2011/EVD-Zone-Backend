<?php

namespace App\Repositories\Seller;

use App\Enums\VendorProductType;
use App\Models\VendorProduct;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Query\JoinClause;
use Prettus\Repository\Eloquent\BaseRepository;

class VendorProductRepository extends BaseRepository
{

    public function __construct(Application $app, private ProductRepository $productRepository)
    {
        parent::__construct($app);
    }


    public function getFirstByProductId($productId, $type)
    {
        return $this->model
            ->where('product_id', $productId)
            ->where('type', $type)
            ->first();
    }

    public function showByVendorIdAndProductId($productId, $vendorId, $type)
    {
        return $this->model
            ->where('vendor_id', $vendorId)
            ->where('product_id', $productId)
            ->where('type', $type)
            ->first();
    }


    public function model(): string
    {
        return VendorProduct::class;
    }
}
