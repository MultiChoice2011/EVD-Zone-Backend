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

class LikeCardService implements OnlineShoppingInterface, ServiceBalanceInterface, ServiceStockInterface, ServiceProductDetailedInfoInterface
{
    private $likeCardConfigration;
//    public function __construct(
//        protected readonly IntegrationRepository $integrationRepository
//    )
//    {
//        // get configuration of this service
//        $this->likeCardConfigration = $this->integrationRepository->showByName('like_card');
//    }

    public function __construct($likeCardConfigration)
    {
        $this->likeCardConfigration = $likeCardConfigration;
    }

    public function checkBalance()
    {
        try {
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'multipart/form-data',
                    'Accept' => 'application/json'
                ],
            ]);

            $response = $client->post($this->likeCardConfigration->keys['url'] . 'online/check_balance', [
                'multipart' => [
                    [
                        'name' => 'deviceId',
                        'contents' => $this->likeCardConfigration->keys['device_id'],
                    ],
                    [
                        'name' => 'email',
                        'contents' => $this->likeCardConfigration->keys['email'],
                    ],
                    [
                        'name' => 'password',
                        'contents' => $this->likeCardConfigration->keys['password'],
                    ],
                    [
                        'name' => 'securityCode',
                        'contents' => $this->likeCardConfigration->keys['security_code'],
                    ],
                    [
                        'name' => 'langId',
                        'contents' => '1',
                    ]
                ],
            ]);

            $response = json_decode($response->getBody(), true);
            Log::info("likecard");
            Log::info($response);
            if ($response['response'] != 1)
                return false;
            return [
                'balance' => $response['balance'],
                'balance_currency' => $response['currency']
            ];
        } catch (Exception|GuzzleException $e) {
            Log::info($e);
            return false;
        }

    }

    public function categoriesList()
    {
        try {
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'multipart/form-data',
                    'Accept' => 'application/json'
                ],
            ]);

            $response = $client->post($this->likeCardConfigration->keys['url'] . 'online/categories', [
                'multipart' => [
                    [
                        'name' => 'deviceId',
                        'contents' => $this->likeCardConfigration->keys['device_id'],
                    ],
                    [
                        'name' => 'email',
                        'contents' => $this->likeCardConfigration->keys['email'],
                    ],
                    [
                        'name' => 'password',
                        'contents' => $this->likeCardConfigration->keys['password'],
                    ],
                    [
                        'name' => 'securityCode',
                        'contents' => $this->likeCardConfigration->keys['security_code'],
                    ],
                    [
                        'name' => 'langId',
                        'contents' => '1',
                    ]
                ],
            ]);

            Log::info($response->getBody());
            return json_decode($response->getBody(), true);
        } catch (Exception|GuzzleException $e) {
            Log::info($e);
            return $e;
        }

    }

    public function productsList($categoryId=266)
    {
        try {
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'multipart/form-data',
                    'Accept' => 'application/json'
                ],
            ]);

            $response = $client->post($this->likeCardConfigration->keys['url'] . 'online/products', [
                'multipart' => [
                    [
                        'name' => 'deviceId',
                        'contents' => $this->likeCardConfigration->keys['device_id'],
                    ],
                    [
                        'name' => 'email',
                        'contents' => $this->likeCardConfigration->keys['email'],
                    ],
                    [
                        'name' => 'password',
                        'contents' => $this->likeCardConfigration->keys['password'],
                    ],
                    [
                        'name' => 'securityCode',
                        'contents' => $this->likeCardConfigration->keys['security_code'],
                    ],
                    [
                        'name' => 'langId',
                        'contents' => '1',
                    ],
                    [
                        'name' => 'categoryId',
                        'contents' => $categoryId,
                    ],
                    //    [
                    //        'name' => 'ids',
                    //        'contents' => ['693'],
                    //    ]
                ],
            ]);

            Log::info($response->getBody());
            return json_decode($response->getBody(), true);
        } catch (Exception|GuzzleException $e) {
            Log::info($e);
            return $e;
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

    public function productDetailedInfo($productId)
    {
        try {
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'multipart/form-data',
                    'Accept' => 'application/json'
                ],
            ]);

            $response = $client->post($this->likeCardConfigration->keys['url'] . 'online/products', [
                'multipart' => [
                    [
                        'name' => 'deviceId',
                        'contents' => $this->likeCardConfigration->keys['device_id'],
                    ],
                    [
                        'name' => 'email',
                        'contents' => $this->likeCardConfigration->keys['email'],
                    ],
                    [
                        'name' => 'password',
                        'contents' => $this->likeCardConfigration->keys['password'],
                    ],
                    [
                        'name' => 'securityCode',
                        'contents' => $this->likeCardConfigration->keys['security_code'],
                    ],
                    [
                        'name' => 'langId',
                        'contents' => '1',
                    ],
                    //    [
                    //        'name' => 'categoryId',
                    //        'contents' => '59',
                    //    ],
                    [
                        'name' => 'ids[]',
                        'contents' => $productId,
                    ]
                ],
            ]);

            $response = json_decode($response->getBody(), true);
            Log::info($response);
            if ($response['response'] != 1)
                return false;
            return [
                'product_id' => $response['data'][0]['productId'],
                'price_before_vat' => $response['data'][0]['productPriceWithoutVat'],
                'vat_amount' => $response['data'][0]['vatAmount'],
                'price_after_vat' => $response['data'][0]['productPrice'],
                'currency' => $response['data'][0]['productCurrency'],
                'available' => $response['data'][0]['available'],
            ];
        } catch (Exception|GuzzleException $e) {
            return false;
        }

    }


    public function purchaseProduct($requestData)
    {
        $order = ['products' => [], 'quantity' => 0, 'price' => 0];
        for ($index = 1; $index <= $requestData['quantity']; $index++) {
            try {
                $currentTime = time();
                $hashValue = hash('sha256',
                    $currentTime .
                    $this->likeCardConfigration->keys['email'] .
                    $this->likeCardConfigration->keys['phone'] .
                    $this->likeCardConfigration->keys['hash_key']);
                $client = new Client([
                    'headers' => [
                        'Content-Type' => 'multipart/form-data',
                        'Accept' => 'application/json'
                    ],
                ]);

                $response = $client->post($this->likeCardConfigration->keys['url'] . 'online/create_order', [
                    'multipart' => [
                        [
                            'name' => 'deviceId',
                            'contents' => $this->likeCardConfigration->keys['device_id'],
                        ],
                        [
                            'name' => 'email',
                            'contents' => $this->likeCardConfigration->keys['email'],
                        ],
                        [
                            'name' => 'password',
                            'contents' => $this->likeCardConfigration->keys['password'],
                        ],
                        [
                            'name' => 'securityCode',
                            'contents' => $this->likeCardConfigration->keys['security_code'],
                        ],
                        [
                            'name' => 'langId',
                            'contents' => '1',
                        ],
                        [
                            'name' => 'productId',
                            'contents' => $requestData['product_id'],
                            //"not allowed to buy the live products, try with product id=376"
                        ],
                        [
                            'name' => 'referenceId',
                            'contents' => $currentTime,
                        ],
                        [
                            'name' => 'time',
                            'contents' => $currentTime,
                        ],
                        [
                            'name' => 'hash',
                            'contents' => $hashValue,
                        ],
                        [
                            'name' => 'quantity',
                            'contents' => '1',
                        ]

                    ]
                ]);

                //     $responseData = json_decode($response->getBody(), true);
                //    if ($responseData) {
                //        foreach ($responseData['serials'] as &$serial) {
                //            $serial['serialCodeOriginal'] = $this->decryptSerial($serial['serialCode']);
                //        }
                //    }

                $response = json_decode($response->getBody(), true);
                Log::info($response);
                if ($response['response'] != 1){
                    break;
                }
                $date = Carbon::createFromFormat('d/m/Y', $response['serials'][0]['validTo']);
                Log::info($date);
                $expiringDate = $date->format('Y-m-d');
                Log::info($expiringDate);
                $order['products'][] = [
                    'price_before_vat' => $response['orderPriceWithoutVat'],
                    'vat_amount' => $response['vatAmount'],
                    'price_after_vat' => $response['orderPrice'],
                    'currency' => null,
                    'scratching' => $this->decryptSerial($response['serials'][0]['serialCode']),
                    'serial' => empty($response['serials'][0]['serialNumber']) ? $response['serials'][0]['serialId'] : $response['serials'][0]['serialNumber'],
                    'buying' => Carbon::now(),
                    'expiring' => $expiringDate ?? Carbon::now()->addYear(),
                    'status' => (isset($requestData['forAutofill']) && $requestData['forAutofill'] == 1) ? ProductSerialType::getTypeFree() : ProductSerialType::getTypeSold(),
                    'invoice_id' => $requestData['invoice_id'],
                    'product_id' => $requestData['original_product_id'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                Log::info($order['products']);
                $order['quantity']++;
                $order['price'] += $response['orderPrice'];
            } catch (Exception|GuzzleException $e) {
                Log::info('likecard purchase');
                Log::info($e);
                continue;
            }
        }
        Log::info(json_encode($order));
        return $order;
    }

    public function orders($requestData)
    {
        try {
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'multipart/form-data',
                    'Accept' => 'application/json'
                ],
            ]);

            $response = $client->post($this->likeCardConfigration->keys['url'] . 'online/orders', [
                'multipart' => [
                    [
                        'name' => 'deviceId',
                        'contents' => $this->likeCardConfigration->keys['device_id'],
                    ],
                    [
                        'name' => 'email',
                        'contents' => $this->likeCardConfigration->keys['email'],
                    ],
                    [
                        'name' => 'password',
                        'contents' => $this->likeCardConfigration->keys['password'],
                    ],
                    [
                        'name' => 'langId',
                        'contents' => '1',
                    ],
                    [
                        'name' => 'securityCode',
                        'contents' => $this->likeCardConfigration->keys['security_code'],
                    ],

                ]
            ]);

            Log::info($response->getBody());
            //return json_decode($response->getBody(), true);
            $responseData = json_decode($response->getBody(), true);
            if ($responseData){
                foreach ($responseData['serials'] as &$serial){
                    $serial['serialCodeOriginal'] = $this->decryptSerial($serial['serialCode']);
                }
            }
            return $responseData;
        } catch (Exception|GuzzleException $e) {
            Log::info($e);
            return $e;
        }

    }

    public function orderDetails($requestData)
    {
        try {
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'multipart/form-data',
                    'Accept' => 'application/json'
                ],
            ]);

            $response = $client->post($this->likeCardConfigration->keys['url'] . 'online/orders/details', [
                'multipart' => [
                    [
                        'name' => 'deviceId',
                        'contents' => $this->likeCardConfigration->keys['device_id'],
                    ],
                    [
                        'name' => 'email',
                        'contents' => $this->likeCardConfigration->keys['email'],
                    ],
                    [
                        'name' => 'password',
                        'contents' => $this->likeCardConfigration->keys['password'],
                    ],
                    [
                        'name' => 'securityCode',
                        'contents' => $this->likeCardConfigration->keys['security_code'],
                    ],
                    [
                        'name' => 'langId',
                        'contents' => '1',
                    ],
                    [
                        'name' => 'orderId',
                        'contents' => $requestData->order_id,
                    ],
                    //    [
                    //        'name' => 'referenceId',
                    //        'contents' => $requestData->reference_id,
                    //    ]

                ]
            ]);

            Log::info($response->getBody());
            //return json_decode($response->getBody(), true);
            $responseData = json_decode($response->getBody(), true);
            if ($responseData){
                foreach ($responseData['serials'] as &$serial){
                    $serial['serialCodeOriginal'] = $this->decryptSerial($serial['serialCode']);
                }
            }
            return $responseData;
        } catch (Exception|GuzzleException $e) {
            Log::info($e);
            return $e;
        }

    }








    private function decryptSerial($encrypted_txt){
        $secret_key = $this->likeCardConfigration->keys['secret_key'];
        $secret_iv = $this->likeCardConfigration->keys['Secret_iv'];
        $encrypt_method = 'AES-256-CBC';
        $key = hash('sha256', $secret_key);

        //iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        return openssl_decrypt(base64_decode($encrypted_txt), $encrypt_method, $key, 0, $iv);
    }



}
