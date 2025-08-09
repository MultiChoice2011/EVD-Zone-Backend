<?php

namespace App\Services\General\OnlineShoppingIntegration;

use App\Contracts\OnlineShoppingInterface;
use App\Contracts\ServiceBalanceInterface;
use App\Contracts\ServiceProductDetailedInfoInterface;
use App\Contracts\ServiceStockInterface;
use App\Enums\ProductSerialType;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class OneCardService implements OnlineShoppingInterface, ServiceBalanceInterface, ServiceStockInterface, ServiceProductDetailedInfoInterface
{
    private $oneCardConfigration;
//    public function __construct(
//        protected readonly IntegrationRepository $integrationRepository
//    )
//    {
//        // get configuration of this service
//        $this->oneCardConfigration = $this->integrationRepository->showByName('one_card');
//    }
    public function __construct($oneCardConfigration)
    {
        // get configuration of this service
        $this->oneCardConfigration = $oneCardConfigration;
    }

    public function checkBalance()
    {
        try {
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
            ]);
            $response = $client->post($this->oneCardConfigration->keys['url'] . 'check-balance', [
                'json' => [
                    'resellerUsername' => $this->oneCardConfigration->keys['user_name'],
                    'password' => md5($this->oneCardConfigration->keys['user_name'].$this->oneCardConfigration->keys['secret_key']),
                ],
            ]);

            $response = json_decode($response->getBody(), true);
            Log::info($response);
            if (!$response)
                return false;
            return [
                'balance' => $response['balance'],
                'balance_currency' => $response['currency']
            ];
        } catch (Exception|GuzzleException $e) {
            return false;
        }

    }

    public function productsList($categoryId=null)
    {
        try {
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
            $response = $client->post($this->oneCardConfigration->keys['url'] . 'detailed-products-list', [
                'json' => [
                    'resellerUsername' => $this->oneCardConfigration->keys['user_name'],
                    'password' => md5($this->oneCardConfigration->keys['user_name'].$this->oneCardConfigration->keys['merchant_id'].$this->oneCardConfigration->keys['secret_key']),
                    'merchantId' => $this->oneCardConfigration->keys['merchant_id'],
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (Exception|GuzzleException $e) {
            return false;
        }



    }

    public function productDetailedInfo($productId)
    {
        try {
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
            $response = $client->post($this->oneCardConfigration->keys['url'] . 'product-detailed-info', [
                'json' => [
                    'resellerUsername' => $this->oneCardConfigration->keys['user_name'],
                    'password' => md5($this->oneCardConfigration->keys['user_name'].$productId.$this->oneCardConfigration->keys['secret_key']),
                    'productID' => $productId,
                ],
            ]);

            $response = json_decode($response->getBody(), true);
            Log::info($response);
            if (!$response['status'])
                return false;
            return [
                'product_id' => $response['product']['productID'],
                'price_before_vat' => $response['product']['costPriceBeforeVat'],
                'vat_amount' => $response['product']['costPriceVatAmount'],
                'price_after_vat' => $response['product']['costPriceAfterVat'],
                'currency' => $response['product']['currency'],
                'available' => $response['product']['available'],
            ];
        } catch (Exception|GuzzleException $e) {
            return false;
        }



    }

    public function checkStock(int $productId, int $quantity): bool
    {
        $product = $this->productDetailedInfo($productId);
        if (!$product || !$product['available']){
            return false;
        }
        return true;
    }

    public function purchaseProduct($requestData)
    {
        $order = ['products' => [], 'quantity' => 0, 'price' => 0];
        for ($index = 1; $index <= $requestData['quantity']; $index++) {
            try {
                $resellerRefNumber = rand(1000,9999).time();
                $client = new Client([
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]);
                $response = $client->post($this->oneCardConfigration->keys['url'] . 'purchase-product', [
                    'json' => [
                        'resellerUsername' => $this->oneCardConfigration->keys['user_name'],
                        'password' =>
                            md5($this->oneCardConfigration->keys['user_name'].$requestData['product_id'].$resellerRefNumber.$this->oneCardConfigration->keys['secret_key']),
                        'productID' => $requestData['product_id'],
                        'resellerRefNumber'=> $resellerRefNumber,
                        'terminalId'=> $requestData['patch_number']
                    ],
                ]);
                Log::info('onecard');
                $response = json_decode($response->getBody(), true);
                Log::info($response);
                if (! $response['status']){
                    continue;
                }
                $scratching = $response['pin'];
                if (isset($response['username']) && $response['username'] != ''){
                    $scratching = $scratching . '-pin:' . $response['username'];
                }
                $order['products'][] = [
                    'price_before_vat' => $response['costPriceBeforeVat'],
                    'vat_amount' => $response['costPriceVatAmount'],
                    'price_after_vat' => $response['costPriceAfterVat'],
                    'currency' => $response['currency'],
                    'serial' => $response['serial'],
                    'scratching' => $scratching,
                    'buying' => Carbon::now(),
                    'expiring' => $response['itemExpirationDate'] ?? Carbon::now()->addYear(),
                    'status' => (isset($requestData['forAutofill']) && $requestData['forAutofill'] == 1) ? ProductSerialType::getTypeFree() : ProductSerialType::getTypeSold(),
                    'invoice_id' => $requestData['invoice_id'],
                    'product_id' => $requestData['original_product_id'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $order['quantity']++;
                $order['price'] += $response['costPriceAfterVat'];
            } catch (Exception|GuzzleException $e) {
                Log::info('onecard purchase');
                Log::info($e);
                continue;
            }
        }
        Log::info(json_encode($order));
        return $order;
    }

    public function orderDetails($resellerRefNumber)
    {
        try {
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
            $response = $client->post($this->oneCardConfigration->keys['url'] . 'check-transaction-status', [
                'json' => [
                    'resellerUsername' => $this->oneCardConfigration->keys['user_name'],
                    'password' =>
                        md5($this->oneCardConfigration->keys['user_name'].$resellerRefNumber.$this->oneCardConfigration->keys['secret_key']),
                    'resellerRefNumber'=> $resellerRefNumber
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (Exception|GuzzleException $e) {
            return false;
        }
    }

    public function orders($requestData)
    {
        try {
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
            $response = $client->post($this->oneCardConfigration->keys['url'] . 'integration/reconcile', [
                'json' => [
                    'resellerUsername' => $this->oneCardConfigration->keys['user_name'],
                    'password' =>
                        md5($this->oneCardConfigration->keys['user_name'].$requestData['dateFrom'].$requestData['dateTo'].$requestData['isSuccessful'].$this->oneCardConfigration->keys['secret_key']),
                    'dateFrom'=> $requestData['dateFrom'],
                    'dateTo'=> $requestData['dateTo'],
                    'isSuccessful'=> $requestData['isSuccessful'],
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (Exception|GuzzleException $e) {
            return false;
        }
    }

}
