<?php

namespace App\Services\General\OnlineShoppingIntegration;

use App\Contracts\OnlineShoppingInterface;
use App\Contracts\ServiceBalanceInterface;
use App\Contracts\ServiceStockInterface;
use App\Enums\ProductSerialType;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class MintrouteVoucherService implements OnlineShoppingInterface, ServiceBalanceInterface, ServiceStockInterface
{
    private $configration;

    public function __construct($configration)
    {
        // get configuration of this service
        $this->configration = $configration;
    }

    protected function prepareAuthorizationString($arrayData)
    {
        $httpMethod = 'POST';
        $queryString = http_build_query($arrayData);
        $currentTime = Carbon::now('UTC');
        $formattedTime = $currentTime->format('Ymd\THi');

        $string_to_sign = $httpMethod . $queryString . $formattedTime;

        $signature = base64_encode(hash_hmac('sha256', $string_to_sign, $this->configration->keys['private_key'], true));

        $formattedTime = $currentTime->format('Ymd');
        return
            'algorithm="hmac-sha256",credential="'. $this->configration->keys['access_key'] .'/'. $formattedTime .'",signature="'. $signature .'"';

    }

    protected function prepareHeaders($arrayData) :array
    {
        $authorizationString = $this->prepareAuthorizationString($arrayData);
        $currentTime = Carbon::now('UTC');
        $formattedTime = $currentTime->format('Ymd\THis\Z');
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => $authorizationString,
            'X-Mint-Date' => $formattedTime
        ];
    }

    public function checkBalance()
    {
        try {
            $arrayData = [
                'username' => $this->configration->keys['username'],
                'data' => [
                    'currency' => $this->configration->keys['currency']
                ],
            ];
            $client = new Client([
                'headers' => $this->prepareHeaders($arrayData),
            ]);
            $response = $client->post($this->configration->keys['url_voucher'] . 'vendor/api/get_current_balance', [
                'json' => $arrayData,
            ]);

            $result = json_decode($response->getBody(), true);
            Log::info($result);
            if (!$result)
                return false;
            return [
                'balance' => $result['data']['available_balance'],
                'balance_currency' => $result['data']['currency']
            ];
        } catch (Exception|GuzzleException $e) {
            Log::info($e->getMessage());
            return false;
        }

    }

    public function checkStock(int $productId, int $quantity): bool
    {
        $product = $this->stock($productId);
        if (!$product || !$product['status']){
            return false;
        }
        return true;
    }

    public function stock($productId)
    {
        try {
            $arrayData = [
                'username' => $this->configration->keys['username'],
                'data' => [
                    'ean' => (string)$productId,
                ],
            ];

            Log::info(now());
            Log::info($arrayData);

            $client = new Client([
                'headers' => $this->prepareHeaders($arrayData),
            ]);
            $response = $client->post($this->configration->keys['url_voucher'] . 'vendor/api/stock', [
                'json' => $arrayData,
            ]);

            $result = json_decode($response->getBody(), true);
            Log::info(json_encode($response));
            Log::info($response->getBody());
            Log::info($result);

            return $result;
        } catch (Exception|GuzzleException $e) {
            return false;
        }
    }

    public function purchaseProduct($requestData)
    {
        $order = ['products' => [], 'quantity' => 0, 'price' => 0];
        $requestData['quantity'] = $requestData['quantity'] > 10 ? 10 : $requestData['quantity'];
        for ($index = 1; $index <= $requestData['quantity']; $index++) {
            try {
                $time = time();
                $arrayData = [
                    'username' => $this->configration->keys['username'],
                    'data' => [
                        'ean' => (string)$requestData['product_id'],
                        'order_id' => (string)($time . 00 . $requestData['original_product_id']),
                        'terminal_id' => 'purple_card',
                        'request_type' => 'purchase',
                        // 'response_type' => 'short'
                    ],
                ];

                Log::info(now());
                Log::info($arrayData);

                $client = new Client([
                    'headers' => $this->prepareHeaders($arrayData),
                ]);
                $response = $client->post($this->configration->keys['url_voucher'] . 'voucher/v2/api/voucher', [
                    'json' => $arrayData,
                ]);

                $result = json_decode($response->getBody(), true);
                Log::info(json_encode($response));
                Log::info($response->getBody());
                Log::info($result);
                // return $result;
                if (! $result['status'] || ! $result['data']){
                    break;
                }
                $order['products'][] = [
                    'price_before_vat' => floatval($result['data']['vendor_pre_balance']) - floatval($result['data']['vendor_post_balance']),
                    'vat_amount' => 0,
                    'price_after_vat' => floatval($result['data']['vendor_pre_balance']) - floatval($result['data']['vendor_post_balance']),
                    'currency' => $result['data']['denomination_currency'],
                    'serial' => $result['data']['voucher']['serial_number'],
                    'scratching' => $result['data']['voucher']['pin_code'],
                    'buying' => Carbon::now(),
                    'expiring' => $result['data']['voucher']['expiring'] ?? Carbon::now()->addYear(),
                    'status' => (isset($requestData['forAutofill']) && $requestData['forAutofill'] == 1) ? ProductSerialType::getTypeFree() : ProductSerialType::getTypeSold(),
                    'invoice_id' => $requestData['invoice_id'],
                    'product_id' => $requestData['original_product_id'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $order['quantity']++;
                $order['price'] += floatval($result['data']['vendor_pre_balance']) - floatval($result['data']['vendor_post_balance']);
            } catch (Exception|GuzzleException $e) {
                Log::info('mintroute voucher purchase');
                Log::info($e);
                continue;
            }
        }
        return $order;

    }

    public function orders($requestData)
    {
        try {
            $arrayData = [
                'username' => $this->configration->keys['username'],
                'data' => [
                    'datefrom'=> $requestData['dateFrom'],
                    'dateto'=> $requestData['dateTo'],
                    'order_type'=> $requestData['order_type'] ?? 'voucher',         // voucher , topup
                    'page'=> $requestData['page'] ?? 1,
                ],
            ];

            Log::info(now());
            Log::info($arrayData);

            $client = new Client([
                'headers' => $this->prepareHeaders($arrayData),
            ]);
            $response = $client->post($this->configration->keys['url_voucher'] . 'vendor/api/get_all_orders', [
                'username' => $this->configration->keys['username'],
                'data' => $arrayData,
            ]);

            return json_decode($response->getBody(), true);
        } catch (Exception|GuzzleException $e) {
            return false;
        }
    }

    public function orderDetails($requestData)
    {
        try {
            $arrayData = [
                'username' => $this->configration->keys['username'],
                'data' => [
                    'order_id'=> $requestData['order_id'],
                    'response_type'=> 'long',           // long , short
                ],
            ];

            Log::info(now());
            Log::info($arrayData);

            $client = new Client([
                'headers' => $this->prepareHeaders($arrayData),
            ]);
            $response = $client->post($this->configration->keys['url_voucher'] . 'vendor/api/order_details', [
                'json' => $arrayData,
            ]);

            $result = json_decode($response->getBody(), true);
            Log::info(json_encode($response));
            Log::info($response->getBody());
            Log::info($result);
        } catch (Exception|GuzzleException $e) {
            return false;
        }
    }



}
