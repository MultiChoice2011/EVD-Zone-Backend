<?php

namespace App\Services\General\OnlineShoppingIntegration;

use App\Contracts\ServiceAccountDetailsInterface;
use App\Contracts\ServiceBalanceInterface;
use App\Contracts\ServiceStockInterface;
use App\Services\BaseService;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class HawaChatService extends BaseService implements ServiceBalanceInterface, ServiceStockInterface, ServiceAccountDetailsInterface
{
    private $configration;
    private Client $client;

    public function __construct($configration)
    {
        $this->client = new Client();
        $this->configration = $configration;
    }


    public function checkBalance()
    {
        $cacheKey = 'hawa_chat_balance';

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $data = [
            'merchantUid' => $this->configration->keys['merchant_uid']
        ];

        $result = $this->makeRequest('GET', 'checkBalance', $data);

        if (!$result || $result['code'] != 200) {
            return false;
        }

        $balanceResponse = [
            'balance' => $result['data'],
            'balance_currency' => null,
        ];

        Cache::put($cacheKey, $balanceResponse, now()->addMinutes(30));

        return $balanceResponse;
    }

    public function checkStock(int $productId, int $quantity): bool
    {
        $product = $this->productDetailedInfo($productId);
        if (!$product || !$product['available']){
            return false;
        }
        return true;
    }

    public function productsList($categoryId=null)
    {
        return false;
    }

    public function productDetailedInfo($productId)
    {
        return [
            'product_id' => 1,
            'price_before_vat' => 1,
            'vat_amount' => 0,
            'price_after_vat' => 1,
            'coins' => 1,
            'currency' => null,
            'available' => 1,
        ];

    }

    public function checkAccountDetails(array $data): array
    {
        $result = ['name'=> null, 'avatar'=> null];
        $account = $this->AccountValidation($data);
        if (!$account) {
            return $result;
        }

        return [
            'name' => $account['nick'],
            'avatar' => $account['avatar'],
        ];

    }
    public function AccountValidation($requestData)
    {
        $data = [
            'merchantUid' => $this->configration->keys['merchant_uid'],
            'hawaNo' => $requestData['hawaNo']
        ];

        $result = $this->makeRequest('GET', 'accountValidation', $data);

        if (!$result || $result['code'] != 200) {
            return false;
        }

        return [
            'nick' => $result['data']['nick'],
            'avatar' => $result['data']['avatar'],
        ];

    }

    public function AccountTopUp($requestData)
    {
        $order = ['topupTransaction' => [], 'quantity' => 0];

        $gold = $this->getCoinsNumber($requestData);
        if (!$gold) {
            return false;
        }

        $uid = Str::uuid();

        $data = [
            'merchantUid' => $this->configration->keys['merchant_uid'],
            'hawaNo' => $requestData['hawaNo'],
            'gold' => $gold,
            'orderNo' => $uid
        ];

        $result = $this->makeRequest('POST', 'charge', $data);

        if (! $result || !$result['data']) {
            return false;
        }

        $order['topupTransaction'][] = [
            'account_id' => $requestData['hawaNo'],
            'transaction_id' => $result['data']['orderNo'],
            'tx_id' => $result['data']['txId'],
        ];
        $order['quantity']++;

        return $order;

    }


    /////////////////////////////////////////////////////////////
    ////////////////////////// Assets ///////////////////////////
    /////////////////////////////////////////////////////////////

    private function makeRequest($method, $endpoint, array $data = []): mixed
    {
        Log::info('hawa_chat_'. $endpoint);

        try {
            $param = '';
            foreach ($data as $key => $value) {
                $param .= $value;
            }

            $param .= $this->configration->keys['secret_key'];
            $sign = $this->generateSignature($param);

            $options = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'sign' => $sign
                ]
            ];

            if (strtoupper($method) === 'POST') {
                $options['json'] = $data;
            } else {
                $options['query'] = $data;
            }

            $response = $this->client->request($method, "{$this->configration->keys['url']}{$endpoint}", $options);

            Log::info(json_decode($response->getBody(), true));
            Log::info($response->getStatusCode());
            Log::info('headers', $response->getHeaders());

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody(), true);
            }

            return false;

        } catch (Exception|GuzzleException $e) {
            Log::error('API request failed', ['error' => $e->getMessage(), 'endpoint' => $endpoint]);
            $this->logException($e);
            return false;
        }
    }


    private function generateSignature(string $param): string
    {
        return md5($param);
    }

    private function getCoinsNumber(array $data): string
    {
        if (isset($data['product'])) {
            $product = $data['product'];
            return $product->coins_number;
        }
        else{
            return 0;
        }
    }


}
