<?php

namespace App\Services\Admin;

use App\Enums\InvoiceType;
use App\Enums\OrderProductType;
use App\Enums\ProductSerialType;
use App\Helpers\FileUpload;
use App\Http\Requests\Admin\ProductRequests\ProductRequest;
use App\Models\ProductImage;
use App\Repositories\Admin\AttributeRepository;
use App\Repositories\Admin\CategoryRepository;
use App\Repositories\Admin\CurrencyRepository;
use App\Repositories\Admin\IntegrationRepository;
use App\Repositories\Admin\InvoiceRepository;
use App\Repositories\Admin\LanguageRepository;
use App\Repositories\Admin\ProductRepository;
use App\Repositories\Admin\ProductSerialRepository;
use App\Repositories\Admin\VendorProductRepository;
use App\Repositories\Admin\VendorRepository;
use App\Services\General\CurrencyService;
use App\Services\General\OnlineShoppingIntegration\IntegrationServiceFactory;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductSerialService
{

    use ApiResponseAble;


    public function __construct(
        private ProductRepository           $productRepository,
        private VendorRepository            $vendorRepository,
        private InvoiceRepository           $invoiceRepository,
        private ProductSerialRepository     $productSerialRepository,
        private VendorProductRepository     $vendorProductRepository,
        private IntegrationRepository       $integrationRepository,
        private CurrencyRepository          $currencyRepository,
    )
    {}


    public function stock_logs($request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $productSerials = $this->productSerialRepository->stock_logs($request);
            if (! $productSerials)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->ApiSuccessResponse($productSerials, "Stock Logs Retrieve  Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    public function stock_logs_invoice($invoice_id): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $productSerials = $this->productSerialRepository->stock_logs_invoice($invoice_id);
            if (! $productSerials)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->ApiSuccessResponse($productSerials, "Stock Logs Retrieve  Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    public function update_stock_logs($id,$request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            $data_request_status = $request->get('status');
            $productSerial = $this->productSerialRepository->update_stock_logs($id,$data_request_status);
            if (! $productSerial)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->ApiSuccessResponse($productSerial, "Stock Logs Updated  Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    public function manualFilling($request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = [];
            // get authed admin
            $authAdmin = Auth::guard('adminApi')->user();
            // validate product_id
            $product = $this->productRepository->active()->find($request->product_id);
            if (! $product)
                return $this->ApiErrorResponse(null, "Invalid product id");
            // validate vendor_id
            $vendor = $this->vendorRepository->approved()->find($request->vendor_id);
            if (! $vendor)
                return $this->ApiErrorResponse(null, "Invalid vendor id");
            // store invoice for these serials
            $invoiceData = [
                'vendor_id' => $request->vendor_id,
                'product_id' => $request->product_id,
                'user_id' => $authAdmin->id,
                'source_type' => $request->source_type,
                'invoice_number' => $request->invoice_number ?? null,
                'type' => InvoiceType::getTypeManual(),
                'status' => 'hold',
                'quantity' => 0
            ];
            $invoice = $this->invoiceRepository->storeInvoice($invoiceData);
            if (! $invoice)
                return $this->ApiErrorResponse(null, "invoice store error!");
            // store serials
            $request->status = ProductSerialType::getTypeHold();
            $request->product_price = $product->price;
            $request->current_currency = $this->currencyRepository->showByLanguageCode('en')?->translations[0]?->code ?? null;
            $returnedArray = $this->productSerialRepository->storeSerials($request, $invoice->id);
            $uniqueProductSerials = $returnedArray['uniqueProductSerials'];
            $repeatedSerials = $returnedArray['repeatedSerials'];
            $data['count_serials_accepts'] = count($uniqueProductSerials);
            $data['count_serials_refused'] = count($repeatedSerials);
            $data['serials_refused'] = $repeatedSerials;
            if (count($uniqueProductSerials) == 0){
                $data['invoice'] = null;
                $this->invoiceRepository->deleteInvoice($invoice->id);
                return $this->ApiSuccessResponse($data, "All serials refused");
            }else{
                // update product and invoice quantity
                $invoice->quantity = count($uniqueProductSerials);
                // $product->quantity += count($uniqueProductSerials);
                // $product->vendor_id = $request->vendor_id;
                $invoice->save();
                // $product->save();
                $data['invoice'] = $invoice;
            }

            DB::commit();
            return $this->ApiSuccessResponse($data, "Stored Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

//    public function autoFilling($request): \Illuminate\Http\JsonResponse
//    {
//        try {
//            DB::beginTransaction();
//            // get authed admin
//            $authAdmin = Auth::guard('adminApi')->user();
//            // get vendor with integration
//            $vendor = $this->vendorRepository->find($request->vendor_id);
//            if (! $vendor)
//                return $this->ApiErrorResponse(null, "Invalid id");
//            $vendor = $vendor->load('integration.keys');
//            if (! $vendor->integration)
//                return $this->ApiErrorResponse(null, "Vendor dont have integration");
//            // get vendor_product_id for this integration
//            $product = $this->productRepository->active()->find($request->product_id);
//            $vendorProduct = $this->vendorProductRepository->showByVendorProductId($request->vendor_id, $request->product_id);
//            // call method from service
//            $vendorIntegrate = $vendor->integration;
//            $service = IntegrationServiceFactory::create($vendorIntegrate);
//            if (! isset($request->quantity)){
//                $data = $service->productsList();
////                $balance = $service->checkBalance();
////                $product = $service->productDetailedInfo($vendorProduct->vendor_product_id);
////                if (! $product)
////                    return $this->ApiErrorResponse(null, __('admin.general_error'));
////                $data = array_merge($balance, $product);
//            }
//            else{
//                $invoice_number = time();
//                // store invoice for these serials
//                $invoiceData = [
//                    'vendor_id' => $request->vendor_id,
//                    'product_id' => $request->product_id,
//                    'user_id' => $authAdmin->id,
//                    'invoice_number' => $invoice_number,
//                    'type' => InvoiceType::getTypeAuto(),
//                    'quantity' => 0
//                ];
//                $invoice = $this->invoiceRepository->storeInvoice($invoiceData);
//                if (! $invoice)
//                    return $this->ApiErrorResponse(null, "invoice store error!");
//                // make order from integration
//                $requestData = [
//                    'product_id' => $vendorProduct->vendor_product_id,
//                    'patch_number' => $invoice_number,
//                    'quantity' => $request->quantity,
//                    'original_product_id' => $request->product_id,
//                    'invoice_id' => $invoice->id,
//                ];
//                $order = $service->purchaseProduct($requestData);
//                if (! $order || count($order['products']) == 0){
//                    $this->invoiceRepository->deleteInvoice($invoice->id);
//                    return $this->ApiErrorResponse(null, __('admin.general_error'));
//                }
//                // store serials
//                $this->productSerialRepository->store($order['products']);
//                // update product and invoice quantity
//                $invoice->quantity = $order['quantity'];
//                $invoice->price = $order['price'];
//                $product->quantity += $order['quantity'];
//                $invoice->save();
//                $product->save();
//                $data = $order;
//            }
//
//            DB::commit();
//            return $this->ApiSuccessResponse($data, "Data...");
//        } catch (Exception $e) {
//            DB::rollBack();
//            //return $this->ApiErrorResponse(null, $e);
//            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
//        }
//    }


    public function autoFilling($request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            // get authed admin
            $authAdmin = Auth::guard('adminApi')->user();
            // get vendor with integration
            $vendor = $this->vendorRepository->find($request->vendor_id);
            if (! $vendor)
                return $this->ApiErrorResponse(null, "Invalid id");
            if (! $vendor->integration_id)
                return $this->ApiErrorResponse(null, "Vendor dont have integration");
            // get vendor_product_id for this integration
            $product = $this->productRepository->active()->find($request->product_id);
            $vendorProduct = $this->vendorProductRepository->showByVendorIdAndProductId($request->product_id, $request->vendor_id, OrderProductType::getTypeSerial());
            if (!$vendorProduct || !$product)
                return $this->ApiErrorResponse(null, "There is no product connected with with this vendor");
            // call method from service
            $vendorIntegrate = $this->integrationRepository->showById($vendor->integration_id);
            $service = IntegrationServiceFactory::create($vendorIntegrate);
            Log::info($vendorIntegrate);
            if (! isset($request->quantity)){
                $balance = $service->checkBalance();
                $product = $service->productDetailedInfo($vendorProduct->vendor_product_id);
                if (! $product)
                    return $this->ApiErrorResponse(null, __('admin.general_error'));
                $data = array_merge($balance, $product);
            }
            else{
                $invoice_number = time();
                // store invoice for these serials
                $invoiceData = [
                    'vendor_id' => $request->vendor_id,
                    'product_id' => $request->product_id,
                    'source_type' => $request->source_type,
                    'user_id' => $authAdmin->id,
                    'invoice_number' => $invoice_number,
                    'type' => InvoiceType::getTypeAuto(),
                    'status' => 'free',
                    'quantity' => 0
                ];
                $invoice = $this->invoiceRepository->storeInvoice($invoiceData);
                if (! $invoice)
                    return $this->ApiErrorResponse(null, "invoice store error!");
                // make order from integration
                $requestData = [
                    'product_id' => $vendorProduct->vendor_product_id,
                    'patch_number' => $invoice_number,
                    'quantity' => $request->quantity,
                    'original_product_id' => $request->product_id,
                    'invoice_id' => $invoice->id,
                    'forAutofill' => 1
                ];
                $order = $service->purchaseProduct($requestData);
                //return $this->ApiSuccessResponse($order, "Data...");

                if (! $order || count($order['products']) == 0){
                    $this->invoiceRepository->deleteInvoice($invoice->id);
                    return $this->ApiErrorResponse(null, __('admin.general_error'));
                }
                // store serials
                $this->productSerialRepository->store($order['products']);
                // update product and invoice quantity
                $invoice->quantity = $order['quantity'];
                $invoice->price = $order['price'];
                $product->quantity += $order['quantity'];
                $product->vendor_id = $request->vendor_id;
                $invoice->save();
                $product->save();
                $data = $order;
            }

            DB::commit();
            return $this->ApiSuccessResponse($data, "Data...");
        } catch (Exception $e) {
            DB::rollBack();
            // return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }




    public function statusInvoiceSerials($request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $productSerialsCount = $this->productSerialRepository->ChangeInvoiceSerialStatus($request);
            if (! $productSerialsCount)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->ApiSuccessResponse(null, "Updated Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }








    private function checkRepeatedSerials($productSerials, $productId)
    {
        $uniqueProductSerials = [];
        $repeatedSerials = [];

        $encounteredSerials = [];
        foreach ($productSerials as $productSerial) {
            $serial = $productSerial['serial'];

            $checkAvailability = $this->productSerialRepository->checkSerialAvailability($productId, $serial);
            if ($checkAvailability) {
                $repeatedSerials[] = $productSerial;
                continue;
            }

            if (in_array($serial, $encounteredSerials)) {
                $repeatedSerials[] = $productSerial;
            } else {
                $uniqueProductSerials[] = $productSerial;
                $encounteredSerials[] = $serial;
            }
        }
        return [
            'uniqueProductSerials' => $uniqueProductSerials,
            'repeatedSerials' => $repeatedSerials
        ];

    }

}
