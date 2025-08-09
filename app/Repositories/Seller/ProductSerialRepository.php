<?php

namespace App\Repositories\Seller;

use App\Enums\ProductSerialType;
use App\Models\Invoice;
use App\Models\ProductSerial;
use Carbon\Carbon;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class ProductSerialRepository extends BaseRepository
{

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function GetFirstExpireFreeSerialsFromProcedure($cartProduct)
    {
        // Call the stored procedure (executes immediately)
        DB::statement('CALL update_product_serials(?, ?)', [$cartProduct->product->id, $cartProduct->quantity]);
        // get these presold serials
        $productSerials = $this->model
            ->where('product_id', $cartProduct->product->id)
            ->where('expiring', '>=', Carbon::now())
            ->where('status', ProductSerialType::getTypePresold())
            ->orderBy('expiring')
            ->limit($cartProduct->quantity)
            ->get();
        if (! $productSerials || $productSerials->isEmpty()) {
            return false;
        }

        // change status to hold
        $serialIds = $productSerials->pluck('id')->toArray();
        Log::info($serialIds);
        return $productSerials;
    }

    public function GetFirstExpireFreeSerials($cartProduct)
    {
       $productSerials = $this->model
            ->where('product_id', $cartProduct->product->id)
            ->where('expiring', '>=', Carbon::now())
            ->where('status', ProductSerialType::getTypeFree())
            ->orderBy('expiring')
            ->limit($cartProduct->quantity)
            // ->lockForUpdate()
            ->get();
        if (! $productSerials || $productSerials->isEmpty()) {
            return false;
        }

        // change status to hold
        $serialIds = $productSerials->pluck('id')->toArray();
        Log::info($serialIds);
        $this->model
            ->whereIn('id', $serialIds)
            ->update(['status' => ProductSerialType::getTypePresold()]);
        return $productSerials;
    }

    public function changeSerialsToSold($serialIds)
    {
        Log::info($serialIds);
        return $this->model
            ->whereIn('id', $serialIds)
            ->update(['status' => ProductSerialType::getTypeSold()]);
    }

    public function store($requestData)
    {
        $fillableArray = array_merge($this->model->getFillable(), ['created_at', 'updated_at']);
        // $filteredData = array_map(fn($item) => Arr::only($item, $fillableArray), $requestData);

        $processedData = [];
        foreach ($requestData as $item) {
            $item = Arr::only($item, $fillableArray);
            $this->model->fill($item);
            $this->model->is_encrypted = 1;
            $processedData[] = $this->model->getAttributes();
        }
        $this->model->insert($processedData);
        // $this->model->insert($filteredData);

        $lastInserted = $this->model
            ->whereIn('product_id', array_column($requestData, 'product_id'))
            ->whereIn('invoice_id', array_column($requestData, 'invoice_id'))
            ->whereIn('serial', array_column($requestData, 'serial'))
            ->orderBy('id', 'desc')
            ->take(count($requestData))
            ->get();

        return $lastInserted;
    }


    public function model(): string
    {
        return ProductSerial::class;
    }
}
