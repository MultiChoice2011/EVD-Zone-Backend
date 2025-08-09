<?php

namespace App\Repositories\Admin;

use App\Models\Category;
use App\Models\Language;
use App\Models\ProductCategory;
use App\Repositories\Admin\CategoryTranslationRepository;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;

class ProductCategoryRepository extends BaseRepository
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function store($requestData)
    {
        return $this->model->updateOrCreate(
            [
                'cart_id' => $requestData->cart_id,
                'product_id' => $requestData->product_id,
            ],
            [
                'cart_id' => $requestData->cart_id,
                'product_id' => $requestData->product_id,
                'quantity' => $requestData->quantity,
            ]
        );
    }



    public function model(): string
    {
        return ProductCategory::class;
    }
}
