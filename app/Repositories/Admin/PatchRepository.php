<?php

namespace App\Repositories\Admin;

use App\Models\Patch;
use App\Models\VendorProduct;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Query\JoinClause;
use Prettus\Repository\Eloquent\BaseRepository;

class PatchRepository extends BaseRepository
{

    public function __construct(Application $app, private ProductRepository $productRepository)
    {
        parent::__construct($app);
    }


    public function index($requestData)
    {

    }

    public function storePatch($requestData)
    {
//        $product = $this->productRepository->find($requestData->product_id);
//        return $this->model->create([
//            'vendor_id' => $requestData->vendor_id,
//            'product_id' => $requestData->product_id,
//            'brand_id' => $product->brand_id,
//            'vendor_product_id' => $requestData->vendor_product_id,
//        ]);
    }



    public function model(): string
    {
        return Patch::class;
    }
}
