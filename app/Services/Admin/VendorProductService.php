<?php

namespace App\Services\Admin;

use App\Enums\Integration\MintrouteIntegrationType;
use App\Repositories\Admin\DirectPurchasePriorityRepository;
use App\Repositories\Admin\DirectPurchaseRepository;
use App\Enums\SellerApprovalType;
use App\Repositories\Admin\IntegrationRepository;
use App\Repositories\Admin\SellerAttachmentRepository;
use App\Repositories\Admin\SellerRepository;
use App\Repositories\Admin\VendorProductRepository;
use App\Repositories\Admin\VendorRepository;
use App\Services\General\OnlineShoppingIntegration\IntegrationServiceFactory;
use App\Traits\ApiResponseAble;
use Exception;
use App\Helpers\FileUpload;
use App\Imports\VendorProductsImport;
use App\Repositories\Admin\LanguageRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;

class VendorProductService
{
    use ApiResponseAble;


    public function __construct(
        private IntegrationRepository                   $integrationRepository,
        private VendorRepository                        $vendorRepository,
        private VendorProductRepository                 $vendorProductRepository,
        private DirectPurchaseRepository                $directPurchaseRepository,
        private DirectPurchasePriorityRepository        $directPurchasePriorityRepository,
        private IntegrationServiceFactory               $integrationServiceFactory,
    )
    {}


    public function getProviderCost($request): JsonResponse
    {
        try {
            DB::beginTransaction();
            // get vendor product details
            $vendor = $this->vendorRepository->show($request->vendor_id);
            if (! $vendor){
                return $this->ApiErrorResponse();
            }
            // get integration config
            $integration = $this->integrationRepository->showById($vendor->integration_id);
            if (! $integration){
                return $this->ApiErrorResponse();
            }
            $integration->name = MintrouteIntegrationType::resolve($integration->name, $request->type);
            // get price from integration
            $service = $this->integrationServiceFactory::create($integration);
            if (! $service){
                return $this->ApiErrorResponse();
            }
            // get product info
            $productInfo = $service->productDetailedInfo($request->vendor_product_id);
            if (! $productInfo){
                return $this->ApiErrorResponse();
            }

            DB::commit();
            return $this->showResponse(['provider_price' => $productInfo['price_after_vat']]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function index($request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $vendorProducts = $this->vendorProductRepository->index($request);
            if (! $vendorProducts)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->showResponse($vendorProducts);
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function storeProduct($request): JsonResponse
    {
        try {
            DB::beginTransaction();
            // store new vendor product
            $vendorProduct = $this->vendorProductRepository->storeProduct($request);
            if (! $vendorProduct)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->ApiSuccessResponse(null, 'Created Successfully...!');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function updateProduct($request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $vendorProductUpdated = $this->vendorProductRepository->updateProduct($request, $id);
            if (! $vendorProductUpdated)
                return $this->ApiErrorResponse(null, __('admin.data_exist_before'));

            DB::commit();
            return $this->ApiSuccessResponse(null, "Updated Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            if ($e instanceof QueryException)
                return $this->ApiErrorResponse(null, 'Exist before');
            else
                return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function deleteProduct($id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $vendorProduct = $this->vendorProductRepository->show($id);
            if (! $vendorProduct){
                return $this->ApiErrorResponse(null, 'This id invalid.');
            }

            $directPurchase = $this->directPurchaseRepository->showByProductId($vendorProduct->product_id);
            if (! $directPurchase){
                return $this->ApiErrorResponse(null, __('admin.general_error'));
            }

            $directPurchasePriority = $this->directPurchasePriorityRepository->showPriorityByVendor($directPurchase->id, $vendorProduct->vendor_id);
            if(! $directPurchasePriority){
                return $this->ApiErrorResponse(null, __('admin.general_error'));
            }

            $this->directPurchasePriorityRepository->deleteVendor($directPurchase->id,$directPurchasePriority);

            $vendorProduct->delete();

            DB::commit();
            return $this->ApiSuccessResponse(null, "Deleted Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function import($request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        // Import the Excel file
        Excel::import(new VendorProductsImport, $request->file('file'));

        return $this->ApiSuccessResponse([],'import product success');
    }





}
