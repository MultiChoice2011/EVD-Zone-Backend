<?php

namespace App\Repositories\Seller;

use App\Models\CustomerProductFavourite;
use App\Models\Favorite;
use App\Models\Seller;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class FavoriteRepository extends BaseRepository
{

    public function __construct(
        Application $app,
        private ProductRepository $productRepository,
    )
    {
        parent::__construct($app);
    }

    public function favourites($requestData, $sellerId)
    {
        $perPage = $requestData->input('per_page', null);

        $favorites = $this->model
            ->with(['category','product'])
            ->where('favoritable_type', Seller::class)
            ->where('favoritable_id', $sellerId)
            ->paginate($perPage ?? PAGINATION_COUNT_SELLER);

        $favorites->map(function($item){
            $item->product = $this->productRepository->getProductDetailsByCategory($item->product_id, $item->category);
        });

        return $favorites;
    }

    public function store($data, $favoritable)
    {
        $favoritable->favorites()->create(['product_id' => $data['product_id'], 'category_id' => $data['category_id']]);
    }




    public function model(): string
    {
        return Favorite::class;
    }
}
