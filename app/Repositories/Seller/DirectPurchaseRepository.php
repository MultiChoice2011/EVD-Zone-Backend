<?php

namespace App\Repositories\Seller;

use App\Models\DirectPurchase;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;

class DirectPurchaseRepository extends BaseRepository
{
    public function __construct(Application $app){
        parent::__construct($app);
    }

    public function showByProductId($productId)
    {
        return $this->model
            ->where('product_id', $productId)
            ->with([
                'directPurchasePriorities' => function ($directPurchasePriorittQuery) {
                    $directPurchasePriorittQuery->orderBy('priority_level');
                },
            ])
            ->first();
    }

    public function model(): string
    {
        return DirectPurchase::class;
    }
}
