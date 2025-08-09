<?php

namespace App\Repositories\Seller;

use App\Models\OrderProductSerial;
use App\Repositories\Admin\LanguageRepository;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

class OrderProductSerialRepository extends BaseRepository
{

    public function __construct(Application $app, private LanguageRepository $languageRepository)
    {
        parent::__construct($app);
    }

    public function purchases($ordersIds)
    {
        $langId = $this->languageRepository->getLangByCode(app()->getLocale())->id;
        return $this->model
            ->join('order_products', 'order_products.id', '=', 'order_product_serials.order_product_id')
            ->join('products', 'products.id', '=', 'order_products.product_id')
            ->leftJoin('product_translations', function (JoinClause $join) use ($langId) {
                $join->on("product_translations.product_id", '=', "products.id")
                    ->where("product_translations.language_id", $langId);
            })
            ->select(
                'order_product_serials.*',
                'products.id as product_id',
                'product_translations.name as product_name',
                'products.price as product_price',
                DB::raw("CONCAT('" . asset('storage/uploads/products') . "', '/', products.image) AS full_image_url"),
            )
            ->whereIn('order_product_serials.order_id', $ordersIds)
            ->paginate(PAGINATION_COUNT_APP);
    }

    public function showByOrderId($orderId)
    {
        $langId = $this->languageRepository->getLangByCode(app()->getLocale())->id;

        return $this->model
            ->join('order_products', 'order_products.id', '=', 'order_product_serials.order_product_id')
            ->join('products', 'products.id', '=', 'order_products.product_id')
            ->leftJoin('product_translations', function (JoinClause $join) use ($langId) {
                $join->on("product_translations.product_id", '=', "products.id")
                    ->where("product_translations.language_id", $langId);
            })
            ->select(
                'order_product_serials.*',
                'products.id as product_id',
                'product_translations.name as product_name',
                'products.price as product_price',
                DB::raw("CONCAT('" . asset('storage/uploads/products') . "', '/', products.image) AS full_image_url"),
            )
            ->where('order_product_serials.order_id', $orderId)
            ->get();
    }

    public function store($serials, $orderProduct)
    {
        $orderProductSerials = [];
        foreach ($serials as $serial){
            $orderProductSerials[] = $this->model->create([
                'order_id' => $orderProduct->order_id,
                'order_product_id' => $orderProduct->id,
                'product_serial_id' => $serial->id,
                'is_encrypted' => 1,
                'serial' => $serial->serial,
                'scratching' => $serial->scratching,
                'buying' => $serial->buying,
                'expiring' => $serial->expiring
            ]);
        }
        return $orderProductSerials;
    }


    public function model(): string
    {
        return OrderProductSerial::class;
    }
}
