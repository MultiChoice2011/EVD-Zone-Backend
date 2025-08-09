<?php

namespace App\Services\General\UrpayWallet;

use App\Repositories\Admin\IntegrationRepository;
use App\Repositories\Front\SettingRepository;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UrpayWalletService
{
    private $urpayConfigration;

//    public function __construct(
//        protected readonly IntegrationRepository $integrationRepository
//    )
//    {
//        // get configuration of this service
//        $this->urpayConfigration = $this->integrationRepository->showByName('msegat');
//    }

    public function __construct($urpayConfigration)
    {
        $this->urpayConfigration = $urpayConfigration;
    }

    private function generateToken()
    {
        try {
            // generate uuid
            $uuid = Str::uuid()->toString();
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'X-Request-Id' => $uuid,
                    'X-Client-Id' => $this->urpayConfigration->client_id,
                    'X-Session-Language' => 'EN',
                ],
            ]);
            $response = $client->post($this->urpayConfigration->url . 'payments/merchant/generatetoken', [
                'json' => [
                    "userName" => $this->urpayConfigration->username,
                    "password" =>  $this->urpayConfigration->password
                ],
            ]);
            return $response->getHeaders();
            //return json_decode($response->getHeaders(), true);
        } catch (Exception|GuzzleException $e) {
            return false;
        }

    }

    public function ecommPaymentInitiate()
    {
        try {
            // generate token for process
            $generateTokenResponse = $this->generateToken();
            // generate uuid
            $uuid = Str::uuid()->toString();
            // get current time
            $transactionId = time().rand(100000,999999);
            // handle Api
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'X-Request-Id' => $uuid,
                    'X-Client-Id' => $this->urpayConfigration->client_id,
                    'X-Session-Language' => 'EN',
                    'X-Security-Token' => $generateTokenResponse['X-Security-Token'][0],
                    'X-Session-Id' => $generateTokenResponse['X-Session-Id'][0],
                ],
            ]);
            $response = $client->post($this->urpayConfigration->url . 'payments/ecomm/initiate', [
                'json' => [
                    "transactionInfo" => [
                        "amount" => [
                            "currency" => "SAR",
                            "value" => 10.0
                        ],
                        "externalTransactionId" => $transactionId,
                        "sourceConsumerMobileNumber" => "+966568595106",
                        "targetMerchantId" => "20293",
                        "targetMerchantWalletNumber" => "99001",
                        "targetTerminalId" => "2446"
                    ]
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (Exception|GuzzleException $e) {
            //Log::info($e->getMessage());
            //return $e->getMessage();
            return false;
        }

    }
    public function ecommPaymentExecute()
    {
        try {
            // generate token for process
            $generateTokenResponse = $this->generateToken();
            // generate uuid
            $uuid = Str::uuid()->toString();
            // get current time
            $transactionId = time().rand(100000,999999);
            // handle Api
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'X-Request-Id' => $uuid,
                    'X-Client-Id' => $this->urpayConfigration->client_id,
                    'X-Session-Language' => 'EN',
                    'X-Security-Token' => $generateTokenResponse['X-Security-Token'][0],
                    'X-Session-Id' => $generateTokenResponse['X-Session-Id'][0],
                    //'X-Verification-Token' => $generateTokenResponse['X-Session-Id'][0],
                ],
            ]);
            $response = $client->post($this->urpayConfigration->url . 'payments/ecomm/execute', [
                'json' => [
                    "transactionInfo" => [
                        "amount" => [
                            "currency" => "SAR",
                            "value" => 10.0
                        ],
                        "externalTransactionId" => $transactionId,
                        "sourceConsumerMobileNumber" => "+966568595106",
                        "targetMerchantId" => "20293",
                        "targetMerchantWalletNumber" => "99001",
                        "targetTerminalId" => "2446"
                    ],
                    "OTPInfo" => [
                        "otp" => "1234",
                        "otpReference" => "27cc11d5-fce6-498c-a8c5-ac71962e15ca",
                    ]
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (Exception|GuzzleException $e) {
            //Log::info($e->getMessage());
            //return $e->getMessage();
            return false;
        }

    }


    public function resendOTP()
    {
        try {
            // generate token for process
            $generateTokenResponse = $this->generateToken();
            // generate uuid
            $uuid = Str::uuid()->toString();
            // get current time
            $transactionId = time().rand(100000,999999);
            // handle Api
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'X-Request-Id' => $uuid,
                    'X-Client-Id' => $this->urpayConfigration->client_id,
                    'X-Session-Language' => 'EN',
                    'X-Security-Token' => $generateTokenResponse['X-Security-Token'][0],
                    'X-Session-Id' => $generateTokenResponse['X-Session-Id'][0],
                ],
            ]);
            $response = $client->post($this->urpayConfigration->url . 'otp/resend', [
                'json' => [
                    "mobileNumber" => "+966563154349",
                    "otpReference" => "27cc11d5-fce6-498c-a8c5-ac71962e15ca",
                    "purpose" => "050"
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (Exception|GuzzleException $e) {
            //Log::info($e->getMessage());
            //return $e->getMessage();
            return false;
        }

    }



}
