<?php

namespace App\Services\General\SmsVerification;

use App\Contracts\ServiceBalanceInterface;
use App\Contracts\SmsVerificationInterface;
use App\Repositories\Admin\IntegrationRepository;
use App\Repositories\Front\SettingRepository;
use App\Traits\ApiResponseAble;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmsMisrService implements SmsVerificationInterface, ServiceBalanceInterface
{
    use ApiResponseAble;

    private $smsmisrConfigration;
//    public function __construct(
//        protected readonly IntegrationRepository $integrationRepository
//    )
//    {
//        // get configuration of this service
//        $this->smsmisrConfigration = $this->integrationRepository->showByName('smsmisr');
//    }

    public function __construct($smsmisrConfigration)
    {
        $this->smsmisrConfigration = $smsmisrConfigration;
    }


    public function checkBalance()
    {
        try {
            $client = new Client([
                'headers' => [
                    'Accept' => 'application/json'
                ],
            ]);
            $response = $client->post(
                $this->smsmisrConfigration->keys['url'] . 'Balance/?username='.$this->smsmisrConfigration->keys['username'].'&password='.$this->smsmisrConfigration->keys['password']
            );
            $result = json_decode($response->getBody(), true);

            return $result['Balance'] ? ['balance' => $result['Balance'],'balance_currency' => ''] : false;
        } catch (Exception|GuzzleException $e) {
            return false;
        }
    }

    public function sendFourDigitOtp($phone)
    {
        // create random code
        $code = rand(1000, 9999);
        return $this->sendOtp($phone, $code);
    }

    public function sendSixDigitOtp($phone)
    {
        // create random code
        $code = rand(100000, 999999);
        return $this->sendOtp($phone, $code);
    }

    public function sendOtp($phone, $code)
    {
        try {
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
            ]);
            $response = $client->post($this->smsmisrConfigration->keys['url'] . 'OTP', [
                'json' => [
                    "environment" => $this->smsmisrConfigration->keys['environment'],
                    "username" =>  $this->smsmisrConfigration->keys['username'],
                    "password" => $this->smsmisrConfigration->keys['password'],
                    "sender" => $this->smsmisrConfigration->keys['sender_id'],
                    "mobile" => $phone,
                    "template" => "0ea82e58642f442b71d24dbd7bbd5fda996072704011a27bbad5c0ac058c8757",
                    "otp" => $code,
                ],
            ]);

            $message = json_decode($response->getBody(), true);
            //Log::info($message);
            $messageFormatted = [];
            if ($message && $message['Code'] == '4901'){
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

            //return $this->ApiErrorResponse(null, $e);
            //return false;
        }
    }


}
