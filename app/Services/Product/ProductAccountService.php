<?php

namespace App\Services\Product;

use App\Enums\GeneralStatusEnum;
use App\Enums\Integration\MintrouteIntegrationType;
use App\Enums\OrderProductType;
use App\Enums\VendorStatus;
use App\Http\Resources\Seller\Product\ProductAccountDetailsResource;
use App\Models\Product;
use App\Models\VendorProduct;
use App\Repositories\Admin\IntegrationRepository;
use App\Repositories\Admin\OptionRepository;
use App\Repositories\Admin\OptionValueRepository;
use App\Repositories\Integration\IntegrationOptionKeyRepository;
use App\Repositories\Option\ProductOptionRepository;
use App\Repositories\Seller\DirectPurchaseRepository;
use App\Repositories\Seller\ProductRepository;
use App\Repositories\Seller\VendorProductRepository;
use App\Services\BaseService;
use App\Services\General\OnlineShoppingIntegration\IntegrationServiceFactory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductAccountService extends BaseService
{

    public function __construct(
        private ProductRepository                   $productRepository,
        private IntegrationOptionKeyRepository      $integrationOptionKeyRepository,
        private DirectPurchaseRepository            $directPurchaseRepository,
        private IntegrationRepository               $integrationRepository,
        private VendorProductRepository             $vendorProductRepository,
        private ProductOptionRepository             $productOptionRepository,
        private OptionValueRepository               $optionValueRepository,
    )
    {}

    public function checkOptionsAccount(array $data): JsonResponse
    {
        try {
            DB::beginTransaction();

            // get this product
            $product = $this->productRepository->showProductByIdAndCategoryId($data['product_id'], $data['category_id']);
            if (! $product){
                return $this->ApiErrorResponse(null, 'Product id for category id not found');
            }

            $accountDetails = $this->checkAccountDetails($data, $product);

            $accountDetails = new ProductAccountDetailsResource($accountDetails);

            DB::commit();
            return $this->showResponse($accountDetails);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($this->logException($e), __('admin.general_error'));
        }
    }

    private function checkAccountDetails(array $data, Product $product): array
    {
        $result = ['name'=> null, 'avatar'=> null];
        if ($product->status == GeneralStatusEnum::getStatusInactive()){
            return $result;
        }
        // get product from direct purchase priorities
        $directPurchase = $this->directPurchaseRepository->showByProductId($product->id);
        // that mean we make this based on priority of live integrations
        if ($directPurchase && $directPurchase->status == GeneralStatusEnum::getStatusActive() && $directPurchase->directPurchasePriorities) {
            foreach ($directPurchase->directPurchasePriorities as $directPurchasePriority) {
                // get vendor product
                $vendorProduct = $this->vendorProductRepository
                    ->showByVendorIdAndProductId($directPurchase->product_id, $directPurchasePriority->vendor_id, OrderProductType::getTypeTopUp());
                if (! $vendorProduct || $vendorProduct->vendor->integration_id == null || $vendorProduct->vendor->status != VendorStatus::getTypeApproved()){
                    continue;
                }
                $vendorIntegrate = $this->integrationRepository->showById($vendorProduct->vendor->integration_id);
                $vendorIntegrate->name = MintrouteIntegrationType::resolve($vendorIntegrate->name, OrderProductType::getTypeTopUp());
                // Initiate Integration Factory
                $service = IntegrationServiceFactory::create($vendorIntegrate);
                if (! method_exists($service, 'checkAccountDetails')){
                    continue;
                }
                // get account data based on vendor connected
                $accountData = $this->getAccountData($data, $vendorProduct);
                if (! $accountData){
                    continue;
                }
                // get account details from service
                $account = $service->checkAccountDetails($accountData);
                $result['name'] = $account['name'];
                $result['avatar'] = $account['avatar'];

                return $result;
            }
        }

        return $result;

    }

    public function getAccountData(array $data, VendorProduct $vendorProduct): bool|array
    {
        Log::info($data);
        $accountData = [];
        $accountData['product_id'] = $vendorProduct->vendor_product_id;
        foreach ($data['product_options'] as $option){
            Log::info($data['product_options']);
            $optionValueVendorKey = null;
            $optionDetails = $this->productOptionRepository->show($option['id']);
            Log::info($optionDetails);
            if (! $optionDetails){
                return false;
            }
            $optionVendorKey = $this->integrationOptionKeyRepository->getValue($vendorProduct->vendor->integration_id, $optionDetails->option->key);
            Log::info($optionVendorKey);
            if (!$optionVendorKey) {
                return false;
            }

            if (!empty($option['option_value_ids'])) {
                $optionValue = $this->optionValueRepository->show($option['option_value_ids'][0]);
                Log::info($optionValue);
                if (! $optionValue){
                    return false;
                }
                $optionValueVendorKey = $this->integrationOptionKeyRepository->getValue($vendorProduct->vendor->integration_id, $optionValue->key);
            }
            // because all integration games want single value
            $accountData[$optionVendorKey->value] = $optionValueVendorKey ? $optionValueVendorKey->value : $option['value'];
        }

        Log::info($accountData);
        return $accountData;
    }


}
