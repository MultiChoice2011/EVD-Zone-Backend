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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SadaLiveService extends BaseService implements ServiceBalanceInterface, ServiceStockInterface, ServiceAccountDetailsInterface
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
        $result = $this->makeRequest('POST', 'checkBalance', []);

        if (!$result || !$result['success'] || !$result['data']) {
            return false;
        }

        return [
            'balance' => $result['data']['coin'],
            'balance_currency' => null,
        ];

    }

    public function productsList($categoryId=null)
    {
        $result = $this->makeRequest('POST', 'getAllProducts', []);

        if (! $result || !$result['success']) {
            return false;
        }

        return $result;

    }

    public function checkStock(int $productId, int $quantity): bool
    {
        $product = $this->productDetailedInfo($productId);
        if (!$product || !$product['available']){
            return false;
        }
        return true;
    }

    public function productDetailedInfo($productId)
    {
        $data = [
            'pid' => $productId,
        ];

        $result = $this->makeRequest('POST', 'getProductInfo', $data);

        if (! $result || !$result['success']) {
            return false;
        }

        return [
            'product_id' => $result['data']['info']['id'],
            'price_before_vat' => $result['data']['info']['price'],
            'vat_amount' => 0,
            'price_after_vat' => $result['data']['info']['price'],
            'coins' => $result['data']['info']['coin'],
            'currency' => null,
            'available' => $result['data']['info']['stock'] > 0 ? 1 : 0,
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
            'name' => $account['name'],
            'avatar' => $account['icon'],
        ];

    }

    public function AccountValidation($requestData)
    {
        $data = [
            'to_uid' => $requestData['to_uid'],
        ];

        $result = $this->makeRequest('POST', 'accountValidation', $data);

        if (! $result || !$result['success'] || !$result['data'] || $result['data']['uid'] == 0) {
            return false;
        }

        return [
            'uid' => $result['data']['uid'],
            'name' => $result['data']['name'],
            'icon' => $result['data']['icon'],
        ];

    }

    public function AccountTopUp($requestData)
    {
        $order = ['topupTransaction' => [], 'quantity' => 0];

        $data = [
            'pid' => $requestData['product_id'],
            'to_uid' => $requestData['uid'],
        ];

        $result = $this->makeRequest('POST', 'createOrder', $data);

        if (! $result || !$result['success'] || !$result['data']) {
            return false;
        }

        $order['topupTransaction'][] = [
            'account_id' => $data['to_uid'],
            'transaction_id' => $result['data']['order_id'],
        ];
        $order['quantity']++;

        return $order;

    }

    public function orderDetails($referenceId)
    {
        $data = [
            'order_id' => $referenceId,
        ];

        $result = $this->makeRequest('POST', 'orderInfo', $data);

        if (!$result || !$result['success'] || !$result['data']) {
            return false;
        }

        return $result;
    }


    /////////////////////////////////////////////////////////////
    ////////////////////////// Assets ///////////////////////////
    /////////////////////////////////////////////////////////////

    private function makeRequest($method, $endpoint, array $data = []): mixed
    {
        Log::info('sada_live_'. $endpoint);

        $query = [
            'ga_uid' => $this->configration->keys['ga_uid'],
            'format' => 'json',
            'nonce' => Str::random(16),
            'timestamp' => Carbon::now()->timestamp,
        ];

        $query = array_merge($query, $data);
        $sign = $this->generateSignature($query, $this->configration->keys['api_key']);
        $query['sign'] = $sign;

        try {
            $response = $this->client->request($method, "{$this->configration->keys['url']}{$endpoint}", [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'query' => $query,
            ]);

            Log::info($query);
            Log::info(json_decode($response->getBody(), true));
            Log::info($response->getStatusCode());

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


    private function generateSignature(array $params, string $apiKey): string
    {
        ksort($params);

        $signStr = '';
        foreach ($params as $key => $value) {
            if (!empty($key) && !empty($value)) {
                $signStr .= "$key=$value&";
            }
        }
        $signStr .= "key=$apiKey";

        return strtoupper(md5($signStr));
    }


}
