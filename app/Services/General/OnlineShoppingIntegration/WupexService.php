<?php

namespace App\Services\General\OnlineShoppingIntegration;

use App\Contracts\OnlineShoppingInterface;
use App\Contracts\ServiceBalanceInterface;
use App\Contracts\ServiceProductDetailedInfoInterface;
use App\Contracts\ServiceStockInterface;
use App\Enums\ProductSerialType;
use App\Services\BaseService;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use App\Models\Integration;

class WupexService extends BaseService implements
    OnlineShoppingInterface,
    ServiceBalanceInterface,
    ServiceStockInterface,
    ServiceProductDetailedInfoInterface
{
    private array $wupexConfig;


    public function __construct()
    {
        $integration = Integration::with('keys')->where('name', 'wupex')->firstOrFail();

        $this->wupexConfig = $integration->keys->pluck('value', 'key')->toArray();
    }


    private function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept-Language' => 'en-US',
            'x-api-key' => $this->wupexConfig['secret_key']
        ];
    }

    public function checkBalance()
    {
        try {
            $client = new Client([
                'headers' => $this->getHeaders()
            ]);

            $url = $this->wupexConfig['url'] . '/api/customer/balance';

            $response = $client->get($url);
            $responseData = json_decode($response->getBody(), true);

            Log::info('Wupex checkBalance response', $responseData);

            if (!isset($responseData['status']) || !$responseData['status'] || !isset($responseData['data'])) {
                return false;
            }

            return [
                // 'balance' => $responseData['data']['balance'],
                'credit' => $responseData['data']['credit'] ?? 0,
                'balance' => $responseData['data']['creditBalance'] ?? ($responseData['data']['balance'] + $responseData['data']['credit']),
                'balance_currency' => 'SAR', // Wupex doesn't specify currency
            ];
        } catch (Exception|GuzzleException $e) {
            $this->logException($e);
            return false;
        }
    }


    public function productsList($categoryId = null)
    {
        try {
            $client = new Client(['headers' => $this->getHeaders()]);
            $response = $client->post($this->wupexConfig['url'] . '/api/product/merchant/invited/list', [
                'json' => ['page' => 1, 'pageSize' => 100],
            ]);
            return json_decode($response->getBody(), true);
        } catch (Exception|GuzzleException $e) {
            $this->logException($e);
            return false;
        }
    }

    public function productDetailedInfo($productId)
    {
        try {
            $client = new Client(['headers' => $this->getHeaders()]);

            // 🟠 Step 1: Search for product in productsList using "search"
            $productSearchPayload = [
                'page' => 1,
                'pageSize' => 20,
                'search' => [
                    [
                        'column' => 'productId',
                        'operator' => 'Equals',
                        'value' => (string)$productId,
                    ]
                ]
            ];

            $searchResponse = $client->post($this->wupexConfig['url'] . '/api/product/merchant/invited/list', [
                'json' => $productSearchPayload,
            ]);

            Log::info('Wupex productDetailedInfo response...');
            Log::info(json_decode($searchResponse->getBody(), true));

            $productList = json_decode($searchResponse->getBody(), true);

            if (! $productList['status'] || empty($productList['data']) || $productList['data'][0]['balance'] == 0) {
                return false;
            }

            return [
                'product_id' => $productList['data'][0]['productId'],
                'price_before_vat' => $productList['data'][0]['price'],
                'vat_amount' => 0,
                'price_after_vat' => $productList['data'][0]['price'],
                'currency' => 'SAR',
                'available' => 1, // check of availability is in above
            ];

        } catch (Exception|GuzzleException $e) {
            $this->logException($e);
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

        try {
            $client = new Client(['headers' => $this->getHeaders()]);

            // 🟠 Step 1: Search for product in productsList using "search"
            $productSearchPayload = [
                'page' => 1,
                'pageSize' => 20,
                'search' => [
                    [
                        'column' => 'productId',
                        'operator' => 'Equals',
                        'value' => (string)$requestData['product_id'],
                    ]
                ]
            ];

            $searchResponse = $client->post($this->wupexConfig['url'] . '/api/product/merchant/invited/list', [
                'json' => $productSearchPayload,
            ]);

            $productList = json_decode($searchResponse->getBody(), true);

            if (
                empty($productList['status']) ||
                !$productList['status'] ||
                empty($productList['data']) ||
                $productList['data'][0]['balance'] == 0
            ) {
                return false;
            }


            // 🟢 Step 2: Pull codes
            Log::info('Starting to pull codes from Wupex');
            $pullCodeUrl = $this->wupexConfig['url'] . '/api/order/pull-codes';
            $merchantCode = $this->wupexConfig['merchant_id'] ?? 'DefaultMerchant';

            Log::info('Pull Code URL:', ['url' => $pullCodeUrl]);
            Log::info('Merchant Code:', ['merchant_id' => $merchantCode]);

            $productRequest = [[
                'merchant' => $merchantCode,
                'sku' => $productList['data'][0]['productCode'],
                'quantity' => $requestData['quantity'],
            ]];
            Log::info('Product request payload:', $productRequest);

            $response = $client->post($pullCodeUrl, ['json' => $productRequest]);
            $responseData = json_decode($response->getBody(), true);

            Log::info('Pull code response:', $responseData);

            if (!isset($responseData['status']) || !$responseData['status']) {
                Log::warning('Pull code response status is invalid');
                return $order;
            }

            $orderName = $responseData['data']['orderName'] ?? null;
            $totalAmount = $responseData['data']['totalAmount'] ?? 0;

            Log::info('Order Name:', ['orderName' => $orderName]);
            Log::info('Total Amount:', ['totalAmount' => $totalAmount]);
            if (!$orderName) {
                Log::warning('Order name is missing');
                return $order;
            }

            // 🟢 Step 3: Fetch order details
            $detailUrl = $this->wupexConfig['url'] . '/api/order/detail?orderName=' . $orderName;
            Log::info('Fetching order details from URL:', ['url' => $detailUrl]);
            $detailResponse = $client->get($detailUrl);
            $detailData = json_decode($detailResponse->getBody(), true);
            Log::info('Detail response data:', $detailData);

            if (!isset($detailData['status']) || !$detailData['status']) {
                Log::warning('Detail response status is invalid');
                return $order;
            }

            $orderData = $detailData['data']['orderData'][0]['serials'] ?? [];
            Log::info('Order serials data:', ['serials' => $orderData]);

            foreach ($orderData as $serialItem) {
                Log::info('Processing serial item:', $serialItem);
                $order['products'][] = [
                    'serial' => $serialItem['serialNumber'] ?? null,
                    'scratching' => $serialItem['serialCode'] ?? 'N/A',
                    'price_before_vat' => $totalAmount,
                    'vat_amount' => 0,
                    'price_after_vat' => $totalAmount,
                    'currency' => $serialItem['currency'] ?? 'SAR',
                    'buying' => Carbon::now(),
                    'expiring' => Carbon::now()->addYear(),
                    'status' => ProductSerialType::getTypeFree(),
                    'invoice_id' => $requestData['invoice_id'],
                    'product_id' => $requestData['original_product_id'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                $order['quantity']++;
                $order['price'] += $totalAmount;
                Log::info('Updated order info:', [
                    'quantity' => $order['quantity'],
                    'price' => $order['price']
                ]);
            }

        } catch (Exception|GuzzleException $e) {
            $this->logException($e);
            throw new \Exception($e->getMessage()); // You can customize error handling
        }

        return $order;
    }





    public function orderDetails($orderName)
    {
        try {
            $client = new Client(['headers' => $this->getHeaders()]);
            $url = $this->wupexConfig['url'] . '/api/order/detail?orderName=' . $orderName;
            $response = $client->get($url);
            return json_decode($response->getBody(), true);
        } catch (Exception|GuzzleException $e) {
            $this->logException($e);
            return false;
        }
    }

    public function orders($requestData)
    {
        try {
            $client = new Client(['headers' => $this->getHeaders()]);
            $response = $client->post($this->wupexConfig['url'] . '/api/order/list', [
                'json' => $requestData,
            ]);
            return json_decode($response->getBody(), true);
        } catch (Exception|GuzzleException $e) {
            $this->logException($e);
            return false;
        }
    }

    public function pullCodes($referenceId = null)
    {
        try {
            $url = $this->wupexConfig['url'] . '/api/order/pull-codes';
            if ($referenceId) {
                $url .= '?referenceId=' . $referenceId;
            }

            $client = new Client(['headers' => $this->getHeaders()]);
            $response = $client->post($url);
            return json_decode($response->getBody(), true);
        } catch (Exception|GuzzleException $e) {
            $this->logException($e);
            return false;
        }
    }

}
