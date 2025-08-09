<?php

namespace App\Repositories\Admin;

use App\Enums\ProductSerialType;
use App\Models\Invoice;
use App\Models\Patch;
use App\Models\ProductSerial;
use App\Models\VendorProduct;
use Carbon\Carbon;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Query\JoinClause;
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

    public function GetFirstExpireFreeSerials($orderProduct, $product)
    {
        $productSerials = $this->model
            ->where('product_id', $product->id)
            ->where('expiring', '>=', Carbon::now())
            ->where('status', ProductSerialType::getTypeFree())
            ->orderBy('expiring')
            ->limit($orderProduct['quantity'])
            ->lockForUpdate()
            ->get();
        if (! $productSerials || count($productSerials) != $orderProduct['quantity'])
            return false;

        // change status to hold
        $serialIds = $productSerials->pluck('id')->toArray();
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

    public function storeSerials($requestData, $invoiceId)
    {
        $uniqueProductSerials = [];
        $repeatedSerials = [];
        $encounteredSerials = [];

        foreach ($requestData->product_serials as $productSerial) {
            $serial = $productSerial['serial'];

            $checkAvailability = $this->checkSerialAvailability($requestData->product_id, $serial);
            if ($checkAvailability || in_array($serial, $encounteredSerials)) {
                $repeatedSerials[] = $productSerial;
            } else {
                $uniqueProductSerials[] = $productSerial;
                $encounteredSerials[] = $serial;
                $this->model->create([
                    'invoice_id' => $invoiceId,
                    'product_id' => $requestData->product_id,
                    'serial' => $productSerial['serial'],
                    'scratching' => $productSerial['scratching'],
                    'status' => $requestData->status,
                    'source_type' => $requestData->source_type,
                    'buying' => $requestData->buying,
                    'expiring' => $requestData->expiring,
                    'price_before_vat' => $productSerial->price_before_vat ?? 0,
                    'vat_amount' => $productSerial->vat_amount ?? 0,
                    'price_after_vat' => $productSerial->price_after_vat ?? $requestData->product_price ?? 0,
                    'currency' => $productSerial->currency ?? $requestData->current_currency ?? null,
                ]);
            }

        }

        return [
            'uniqueProductSerials' => $uniqueProductSerials,
            'repeatedSerials' => $repeatedSerials
        ];
    }

    public function store($requestData)
    {
        $keysToInclude = ['invoice_id', 'product_id', 'serial', 'scratching', 'status', 'buying', 'expiring', 'price_before_vat', 'vat_amount', 'price_after_vat', 'currency', 'barcode_link', 'created_at', 'updated_at'];

        // $filteredData = array_map(fn($item) => Arr::only($item, $keysToInclude), $requestData);
        $processedData = [];
        foreach ($requestData as $item) {
            $item = Arr::only($item, $keysToInclude);
            $this->model->fill($item);
            $this->model->is_encrypted = 1;
            $processedData[] = $this->model->getAttributes();
        }
        return $this->model->insert($processedData);
        // return $this->model->insert($filteredData);
    }

    public function update_stock_logs($id,$data_request_status)
    {
        $productSerial = $this->model->where('id',$id)->first();
        $productSerial->status = $data_request_status;
        $productSerial->save();
        // Now $productSerials will contain all data grouped by invoice_id with count
        return $productSerial;
    }
    public function stock_logs($request)
    {
        $perPage = $request->input('per_page', PAGINATION_COUNT_ADMIN); // Default to PAGINATION_COUNT_ADMIN if not provided
        $searchTerm = $request->input('search', ''); // Default to an empty string if not provided
        $query = Invoice::with(['vendor:id,name','product.translations','user:id,name']);
        // Apply searching
        if ($searchTerm) {
            $query->whereHas('product.translations', function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%');
            });
        }
        return $query->orderByDesc('id')->paginate($perPage);
    }
    public function stock_logs_invoice($invoice_id)
    {
        $logs = $this->model->where('invoice_id', $invoice_id)->paginate(PAGINATION_COUNT_ADMIN);

        $logs->getCollection()->transform(function ($log) {
            $log->scratching = substr($log->scratching, 0, 3) . '***';
            return $log;
        });

        return $logs;
    }

    public function ChangeInvoiceSerialStatus($requestData)
    {
        $invoice = Invoice::where('id', $requestData->invoice_id)->first();
        $invoiceStatus = $invoice->status;
        $invoice->status = $requestData->status;
        $invoice->save();
        $productSerialsCount = $this->model
            ->where('invoice_id', $requestData->invoice_id)
            ->whereIn('status', [
                ProductSerialType::getTypeHold(),
                ProductSerialType::getTypeFree(),
                ProductSerialType::getTypeStopped()
            ])
            ->update([
                'status' => $requestData->status,
            ]);
        $product = $invoice->product;
        if ($requestData->status == ProductSerialType::getTypeFree()){
            $product->quantity += $productSerialsCount;
            $product->save();
        }elseif ($requestData->status == ProductSerialType::getTypeStopped() && $invoiceStatus == ProductSerialType::getTypeFree()){
            $product->quantity -= $productSerialsCount;
            $product->save();
        }else{
            return $productSerialsCount;
        }
        return $productSerialsCount;
    }


    public function checkSerialAvailability($productId, $serial)
    {
        return $this->model
            ->where('product_id', $productId)
            ->where('serial', $serial)
            ->first();
    }

    public function model(): string
    {
        return ProductSerial::class;
    }
}
