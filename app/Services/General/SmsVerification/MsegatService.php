<?php

namespace App\Services\General\SmsVerification;

use App\Contracts\ServiceBalanceInterface;
use App\Contracts\SmsVerificationInterface;
use App\Repositories\Admin\IntegrationRepository;
use App\Repositories\Front\SettingRepository;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MsegatService implements SmsVerificationInterface, ServiceBalanceInterface
{
    private $msegatConfigration;
//    public function __construct(
//        protected readonly IntegrationRepository $integrationRepository
//    )
//    {
//        // get configuration of this service
//        $this->msegatConfigration = $this->integrationRepository->showByName('msegat');
//    }

    public function __construct($msegatConfigration)
    {
        $this->msegatConfigration = $msegatConfigration;
    }

    public function checkBalance()
    {
        try {
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'multipart/form-data; boundary=BOUNDARY',
                    //'Accept' => 'application/json'
                ],
            ]);
            $response = $client->post($this->msegatConfigration->keys['url'] . 'Credits.php', [
                'multipart' => [
                    [
                        'name' => 'userName',
                        'contents' => $this->msegatConfigration->keys['username']
                    ],
                    [
                        'name' => 'apiKey',
                        'contents' => $this->msegatConfigration->keys['api_key']
                    ]
                ]
            ]);
            $result = json_decode($response->getBody(), true);
            return is_numeric($result) ? ['balance' => $result,'balance_currency' => ''] : false;
        } catch (Exception|GuzzleException $e) {
            return false;
        }
    }

    public function sendFourDigitOtp($phone)
    {
        try {
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
            ]);
            $response = $client->post($this->msegatConfigration->keys['url'] . 'sendOTPCode.php', [
                'json' => [
                    "lang" => "En",
                    "userName" =>  $this->msegatConfigration->keys['username'],
                    "number" => $phone,
                    "apiKey" => $this->msegatConfigration->keys['api_key'],
                    "userSender" => $this->msegatConfigration->keys['user_sender']
                ],
            ]);
            $message = json_decode($response->getBody(), true);
            $messageFormatted = [];
            if ($message && $message['code'] == '1'){
                $messageFormatted['code'] = $message['id'];
                $messageFormatted['is_id'] = 1;
                return $messageFormatted;
            }else
                return false;
        } catch (Exception|GuzzleException $e) {
            return false;
        }

    }

    public function verifyOtp($code)
    {
        try {
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
            ]);
            $response = $client->post($this->msegatConfigration->keys['url'] . 'verifyOTPCode.php', [
                'json' => [
                    "lang" => "En",
                    "userName" =>  $this->msegatConfigration->keys['username'],
                    "code" => '1111',
                    "id" => 11 ,
                    "apiKey" => $this->msegatConfigration->keys['api_key'],
                    "userSender" => $this->msegatConfigration->keys['user_sender']
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (Exception|GuzzleException $e) {
            return false;
        }

    }

    public function sendSixDigitOtp($phone)
    {
        try {
            // create random code
            $code = rand(100000, 999999);
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
            ]);
            $response = $client->post($this->msegatConfigration->keys['url'] . 'sendsms.php', [
                'json' => [
                    "msg" => $code . " is your OTP, Do not share it with anyone.",
                    "userName" =>  $this->msegatConfigration->keys['username'],
                    "numbers" => $phone,
                    "apiKey" => $this->msegatConfigration->keys['api_key'],
                    "userSender" => $this->msegatConfigration->keys['user_sender']
                ],
            ]);

            $message = json_decode($response->getBody(), true);
            Log::info($message);
            $messageFormatted = [];
            if ($message && $message['code'] == '1'){
                $messageFormatted['is_sent'] = 1;
                $messageFormatted['code'] = $code;
                $messageFormatted['is_id'] = 0;
                return $messageFormatted;
            }else{
                $messageFormatted['is_sent'] = 0;
                $messageFormatted['code'] = $code;
                $messageFormatted['is_id'] = 0;
                return $messageFormatted;

                //return false;
            }
        } catch (Exception|GuzzleException $e) {
            $messageFormatted['is_sent'] = 0;
            $messageFormatted['code'] = $code;
            $messageFormatted['is_id'] = 0;
            return $messageFormatted;
            //return false;
        }

    }



}
