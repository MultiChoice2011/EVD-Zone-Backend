<?php

namespace App\Services\Order\Helpers;

use App\Enums\GeneralStatusEnum;
use App\Enums\Integration\MintrouteIntegrationType;
use App\Enums\OrderProductType;
use App\Enums\ProductSerialType;
use App\Enums\VendorStatus;
use App\Models\Category;
use App\Models\Integration;
use App\Models\Product;
use App\Models\VendorProduct;
use App\Repositories\Admin\CategoryRepository;
use App\Repositories\Admin\IntegrationRepository;
use App\Repositories\Integration\IntegrationOptionKeyRepository;
use App\Repositories\Seller\DirectPurchaseRepository;
use App\Repositories\Seller\VendorProductRepository;
use App\Services\General\OnlineShoppingIntegration\IntegrationServiceFactory;
use App\Services\Product\ProductAccountService;
use Exception;
use Illuminate\Support\Facades\Log;


class CheckVendorBalanceHelper
{

    public function __construct(
        private DirectPurchaseRepository            $directPurchaseRepository,
        private IntegrationRepository               $integrationRepository,
        private VendorProductRepository             $vendorProductRepository,
        private ProductAccountService               $productAccountService,
        private IntegrationOptionKeyRepository      $integrationOptionKeyRepository,
    )
    {}

    /**
     * @param Product $product
     * @param int $quantity
     * @return bool
     */
    public function checkVendorProductAvailability(
        Product $product,
        Category $category,
        int $quantity = 1,
        mixed $accountData = null,
        bool $fromRequest = false
    ): array
    {
        Log::info('checkVendorProductAvailability - Start', [
            'product_id' => $product->id,
            'category_id' => $category->id,
            'quantity' => $quantity
        ]);

        try {
            $data = ['success' => false, 'general_error' => 0, 'error_stock' => 0, 'error_coding' => 0, 'error_account_validated' => 0];
            if ($product->status == GeneralStatusEnum::getStatusInactive()){
                Log::info('Product is inactive', ['product_id' => $product->id]);
                $data['success'] = false;
                $data['error_stock'] = 1;
                return $data;
            }
            $productSerialsCount = $product->productSerials()->where('status', ProductSerialType::getTypeFree())->count();
            Log::info('Free serials count', ['count' => $productSerialsCount]);

            // get product from direct purchase priorities
            $directPurchase = $this->directPurchaseRepository->showByProductId($product->id);
            // that mean we make this based on priority of live integrations
            if ($directPurchase && $directPurchase->status == GeneralStatusEnum::getStatusActive() && $directPurchase->directPurchasePriorities) {
                Log::info('Direct purchase priorities found', ['product_id' => $product->id]);

                foreach ($directPurchase->directPurchasePriorities as $directPurchasePriority) {
                    Log::info('Checking priority vendor', ['vendor_id' => $directPurchasePriority->vendor_id]);

                    // get vendor product as serials to used it after if all topup fails we make it as serials
                    $vendorProductSerial = $this->vendorProductRepository->showByVendorIdAndProductId($directPurchase->product_id, $directPurchasePriority->vendor_id, OrderProductType::getTypeSerial());
                    if ($vendorProductSerial && $vendorProductSerial->vendor->integration_id != null && $vendorProductSerial->vendor->status == VendorStatus::getTypeApproved()){
                        Log::info('Serial vendor with integration found', ['vendor_id' => $vendorProductSerial->vendor->id]);

                        $vendorIntegrate = $this->integrationRepository->showById($vendorProductSerial->vendor->integration_id);
                        $vendorIntegrate->name = $vendorIntegrate->name == 'mintroute' ? 'mintroute_voucher' : $vendorIntegrate->name;

                        // Initiate Integration Factory
                        $service = IntegrationServiceFactory::create($vendorIntegrate);
                        // Check Vendor Balance
                        $result = $service->checkBalance();
                        Log::info('Vendor balance result', ['result' => $result]);
                        $productPrice = $product->price;
                        if (!$result){
                            continue;
                        }
                        else{
                            if ($result['balance_currency'] !== null){
                                // currency conversion needed
                                $priceConverted = convertCurrency(amount: $productPrice, toCurrency: $result['balance_currency']);
                                Log::info('Converted price', ['converted_price' => $priceConverted]);
                                Log::info('price-converted-' . $priceConverted);
                                if (! $priceConverted){
                                    $data['success'] = false;
                                    $data['general_error'] = 1;
                                    return $data;
                                }
                                if ($result['balance'] <=  $priceConverted){
                                    continue;
                                }

                            }
                            else{
                                // No currency conversion needed
                                if ($result['balance'] < $product->coins_number * $quantity){
                                    continue;
                                }
                            }
                        }
                        // Check product stock
                        $available = $service->checkStock($vendorProductSerial->vendor_product_id, $quantity);
                        Log::info('Stock check for serial vendor', ['available' => $available]);
                        if (!$available){
                            continue;
                        }
                        $data['success'] = true;
                        break;
                    }
                    // get first vendor available for this product with id in this vendor integration
                    $vendorProductType = $category->is_topup == 0 ? OrderProductType::getTypeSerial() : OrderProductType::getTypeTopUp();
                    $vendorProduct = $this->vendorProductRepository->showByVendorIdAndProductId($directPurchase->product_id, $directPurchasePriority->vendor_id, $vendorProductType);
                    if (!$vendorProduct || $vendorProduct->vendor->status != VendorStatus::getTypeApproved()){
                        continue;
                    }
                    // check if it is special-service-vendor that has is_service with 1
                    if (
                        $vendorProduct?->vendor->integration_id == null &&
                        $vendorProduct?->vendor->is_service == 1
                    ) {
                        Log::info('Service vendor matched without integration');
                        // if product as service it is done without any serials
                        $data['success'] = true;
                        break;
                    }
                    if ($vendorProduct->vendor->integration_id == null){
                        continue;
                    }
                    $vendorIntegrate = $this->integrationRepository->showById($vendorProduct->vendor->integration_id);
                    $originalVendorIntegrate = clone $vendorIntegrate;
                    $vendorIntegrate->name = MintrouteIntegrationType::resolve($vendorIntegrate->name, OrderProductType::getTypeSerial());
                    // $vendorIntegrate->name = $vendorIntegrate->name == 'mintroute' ? 'mintroute_voucher' : $vendorIntegrate->name;
                    // Initiate Integration Factory
                    $service = IntegrationServiceFactory::create($vendorIntegrate);
                    if (! $service){
                        continue;
                    }
                    // Check Vendor Balance
                    $result = $service->checkBalance();
                    Log::info('Balance for top-up vendor', ['result' => $result]);
                    $productPrice = $product->price;
                    Log::info($productPrice);
                    if (!$result){
                        continue;
                    }
                    else{
                        if ($result['balance_currency'] !== null){
                            // currency conversion needed
                            $priceConverted = convertCurrency(amount: $productPrice, toCurrency: $result['balance_currency']);
                            Log::info('Converted price for top-up', ['converted_price' => $priceConverted]);
                            Log::info('price-converted-' . $priceConverted);
                            if (! $priceConverted){
                                $data['success'] = false;
                                $data['general_error'] = 1;
                                return $data;
                            }
                            if ($result['balance'] <=  $priceConverted){
                                continue;
                            }

                        }
                        else{
                            Log::info('balance-currency-null');
                            Log::info($product->coins_number);
                            // No currency conversion needed
                            if ($result['balance'] < $product->coins_number * $quantity){
                                continue;
                            }
                        }
                    }

                    // if it is topup, will stop here
                    if ($category->is_topup == 1){
                        Log::info('Top-up category detected');
                        $vendorProduct = $this->vendorProductRepository->showByVendorIdAndProductId($directPurchase->product_id, $directPurchasePriority->vendor_id, $vendorProductType);
                        if (!$vendorProduct || $vendorProduct->vendor->status != VendorStatus::getTypeApproved()){
                            continue;
                        }
                        $vendorIntegrate->name = MintrouteIntegrationType::resolve($originalVendorIntegrate->name, OrderProductType::getTypeTopUp());
                        // Initiate Integration Factory
                        $service = IntegrationServiceFactory::create($vendorIntegrate);
                        if (! $service){
                            continue;
                        }

                        // check account validation
                        if (!$accountData){
                            Log::info('account data not found');
                            $data['success'] = false;
                            $data['general_error'] = 1;
                            return $data;
                        }
                        elseif($fromRequest){
                            Log::info('enter from request');
                            $result = $this->getAccountDataFromRequest($accountData, $vendorProduct);
                        }else{
                            Log::info('enter from model');
                            $result = $this->getAccountDataFromModel($accountData, $vendorProduct);
                        }
                        if (!$result){
                            $data['success'] = false;
                            $data['error_account_validated'] = 1;
                            return $data;
                        }
                        // get account details from service
                        $account = $service->checkAccountDetails($result);
                        if (!$account['name'] && !$account['avatar']){
                            $data['success'] = false;
                            $data['error_account_validated'] = 1;
                            return $data;
                        }
                    }
                    // Check product stock
                    $available = $service->checkStock($vendorProduct->vendor_product_id, $quantity);
                    Log::info('Stock check for vendor', ['available' => $available]);
                    if (!$available){
                        $data['error_coding'] = 1;
                        continue;
                    }
                    $data['success'] = true;
                    break;
                }
                // after end of all priorities check if stock is available
                if (!$data['success'] && $product->quantity >= $quantity && $productSerialsCount >= $quantity){
                    Log::info('Fallback to product local stock');
                    $data['success'] = true;
                }
                return $data;
            }
            elseif ($product->quantity >= $quantity && $productSerialsCount >= $quantity){
                Log::info('No direct purchase, using local product stock');
                $data['success'] = true;
                return $data;
            }
            else{
                Log::info('Product unavailable in all paths');
                $data['success'] = false;
                $data['general_error'] = 1;
                return $data;
            }
        }
        catch (Exception $e) {
            Log::error('Exception in checkVendorProductAvailability', ['message' => $e->getMessage()]);
            $data['success'] = false;
            $data['general_error'] = 1;
            return $data;
        }
    }

    public function getAccountDataFromRequest($data, VendorProduct $vendorProduct): bool|array
    {
        return $this->productAccountService->getAccountData($data, $vendorProduct);
    }

    public function getAccountDataFromModel($cartProduct, VendorProduct $vendorProduct): bool|array
    {
        $data = [];
        $data['product_id'] = $vendorProduct->vendor_product_id;
        foreach ($cartProduct->options as $option){
            $optionValueVendorKey = null;
            $optionVendorKey = $this->integrationOptionKeyRepository->getValue($vendorProduct->vendor->integration_id, $option->optionDetails->key);
            if (!$optionVendorKey) {
                return false;
            }
            $option->load('cartProductOptionValues');
            if ($option->cartProductOptionValues->isNotEmpty()) {
                $optionValueVendorKey = $this->integrationOptionKeyRepository->getValue($vendorProduct->vendor->integration_id, $option->cartProductOptionValues[0]->optionValue->key);
            }
            // because all integration games want single value
            $data[$optionVendorKey->value] = $optionValueVendorKey ? $optionValueVendorKey->value : $option->value;

        }

        return $data;
    }
}
