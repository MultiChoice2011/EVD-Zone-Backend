<?php

namespace App\Services\General\PaymentGateways;

use App\Repositories\Admin\IntegrationRepository;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AlrajhiPaymentService
{
    private $configration;

//    public function __construct(protected readonly IntegrationRepository $integrationRepository)
//    {
//        $this->configration = $this->integrationRepository->showByName('alrajhi_payment');
//    }

    public function __construct($configration)
    {
        $this->configration = $configration;
    }

    public function hostedPayment($requestData)
    {
        $trackId = (string)date('YmdHis');
        $trandata = [[
            "amt" => $requestData->price,
            "action" => "1",
            "password" => $this->configration->keys['tranportal_password'],
            "id" => $this->configration->keys['tranportal_id'],
            "currencyCode" => $this->configration->keys['currency_code'],
            "trackId" => $trackId,
            "responseURL" => route('paymentResultSuccess'),
            "errorURL" => route('paymentResultFaild'),
            //"udf3" => "iframe"
        ]];

        $trandataEncrypted = $this->encryptAES( urlencode(json_encode($trandata)), $this->configration->keys['resource_key'] );

        return $this->processPayment('hosted.htm', $trandataEncrypted, $trackId);
    }

    public function merchentPayment($requestData)
    {
        $trackId = (string)date('YmdHis');
        $trandata = [[
            "amt" => $requestData->price,
            "action" => "1",
            "password" => $this->configration->keys['tranportal_password'],
            "id" => $this->configration->keys['tranportal_id'],
            "currencyCode" => $this->configration->keys['currency_code'],
            "trackId" => $trackId,
            "expYear" => $requestData->exp_year,   //"2023",
            "expMonth" => $requestData->exp_month,      //"12",
            "member" => $requestData->member,      //"Test",
            "cvv2" => $requestData->cvv2,         //"123",
            "cardNo" => $requestData->card_number,       //"5105105105105100",
            "cardType" => "C",
            "responseURL" => route('paymentResultSuccess'),
            "errorURL" => route('paymentResultFaild'),
            "browserJavaEnabled" => "true",
            "browserLanguage" => "en",
            "browserColorDepth" => "48",
            "browserScreenHeight" => "400",
            "browserScreenWidth" => "600",
            "browserTZ" => "0",
            "jsEnabled" => "true",
            "udf6" => "12345",
            "udf7" => "C"
        ]];

        $trandataEncrypted = $this->encryptAES( urlencode(json_encode($trandata)), $this->configration->keys['resource_key'] );

        return $this->processPayment('tranportal.htm', $trandataEncrypted, $trackId);
    }




    public function processRegistration($data)
    {
        try {

            $parseResource = '32907121307932907121307932907121';

            $trandata = [[
                "password" => "M5#yRI@e6i#Lh91",
                "id" => "bDBO1TxrN2a177a",
                "expYear" => $data->exp_year,   //"2023",
                "expMonth" => $data->exp_month,  //"12",
                "cardNo" => $data->card_number,    // "5105105105105100",
                "cardOnFileAction" => "registration"
                //"cardOnFileToken" => "202329293440303"

            ]];

            $trandataEncrypted = $this->encryptAES( urlencode(json_encode($trandata)), $parseResource );
            //return $this->returnData("data", $trandataEncrypted);

            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
            ]);

            $response = $client->post('https://securepayments.alrajhibank.com.sa/pg/payment/tranportal.htm', [
                'json' => [
                    [
                        "id" => "bDBO1TxrN2a177a",
                        "trandata" => $trandataEncrypted,
                    ]
                ]
            ]);

            //return $response_content = json_decode($response->getBody()->getContents(), true);

            return json_decode($response->getBody(), true);

        } catch (\Exception $e) {
            return false;
        }

    }

    public function checkPaymentStatus($data)
    {
        try {

            $parseResource = '32907121307932907121307932907121';

            $trackId = (string)date('YmdHis');
            $trandata = [[
                "amt" => "1.00",
                "action" => "8",
                "password" => "M5#yRI@e6i#Lh91",
                "id" => "bDBO1TxrN2a177a",
                "currencyCode" => "682",
                "trackId" => $trackId,      //"20231022081301",
                "udf5" => "TRANID",
                "transId" => $data->trans_id,          //"232951113017414",
            ]];

            $trandataEncrypted = $this->encryptAES( urlencode(json_encode($trandata)), $parseResource );

            //return $this->returnData("data", $trandata);
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
            ]);

            //$response = $client->post('https://securepayments.alrajhibank.com.sa/pg/payment/hosted.htm', [
            $response = $client->post('https://securepayments.alrajhibank.com.sa/pg/payment/tranportal.htm', [
                'json' => [

                    [
                        "id" => "bDBO1TxrN2a177a",
                        "trandata" => $trandataEncrypted,
                    ]


                ]
            ]);

            return $response_content = json_decode($response->getBody()->getContents(), true);

            //return $response->getBody(); //json_decode($response->getBody(), true);

        } catch (\Exception $e) {
            return false;
        }

    }

    public function paymentRefund($data)
    {
        try {

            $parseResource = '32907121307932907121307932907121';

            $trackId = (string)date('YmdHis');
            $trandata = [[
                "amt" => "1.00",
                "action" => "2",
                "password" => "M5#yRI@e6i#Lh91",
                "id" => "bDBO1TxrN2a177a",
                "currencyCode" => "682",
                "trackId" => $trackId,      //"20231022081301",
                "udf5" => "TRANID",
                "transId" => $data->trans_id,          //"232951113017414",
            ]];

            $trandataEncrypted = $this->encryptAES( urlencode(json_encode($trandata)), $parseResource );

            //return $this->returnData("data", $trandataEncrypted);
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
            ]);

            //$response = $client->post('https://securepayments.alrajhibank.com.sa/pg/payment/hosted.htm', [
            $response = $client->post('https://securepayments.alrajhibank.com.sa/pg/payment/tranportal.htm', [
                'json' => [

                    [
                        "id" => "bDBO1TxrN2a177a",
                        "trandata" => $trandataEncrypted,
                    ]


                ]
            ]);

            return $response_content = json_decode($response->getBody()->getContents(), true);

            //return $response->getBody(); //json_decode($response->getBody(), true);

        } catch (\Exception $e) {
            return false;
        }

    }

    public function invoicePayment($data)
    {
        try {

            $parseResource = '32907121307932907121307932907121';

            $invoiceId = (string)date('YmdHis');
            $trackId = "132".(string)date('YmdHis');
            /* Dedicated Invoice */
            /*
            $trandata = [[
                "password" => "M5#yRI@e6i#Lh91",
                "id" => "bDBO1TxrN2a177a",
                "currencyCode" => 682,
                "invoiceId" => $invoiceId,
                "itemDesc" => "TestTest",
                "invoiceType" => "D",
                "buyerName" => "KareemAyman",
                "amt" => 1.00,
                "email" => "karemayman335@gmail.com",
                "mobile" => "01097322033"
            ]];
            */

            /* Open Invoice */
            $trandata = [[
                "password" => "M5#yRI@e6i#Lh91",
                "id" => "bDBO1TxrN2a177a",
                "currencyCode" => 682,
                "invoiceId" => $invoiceId,
                "itemDesc" => "TestTest",
                "invoiceType" => "O",
                "amt" => 1.00,
            ]];


            $trandataEncrypted = $this->encryptAES( urlencode(json_encode($trandata)), $parseResource );

            //return $this->returnData("data", $trandataEncrypted);
            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
            ]);

            //$response = $client->post('https://securepayments.alrajhibank.com.sa/pg/payment/hosted.htm', [
            $response = $client->post('https://securepayments.alrajhibank.com.sa/pg/payment/invoice.htm', [
                'json' => [

                    [
                        "id" => "bDBO1TxrN2a177a",
                        "trandata" => $trandataEncrypted,
                    ]


                ]
            ]);

            return $response_content = json_decode($response->getBody()->getContents(), true);

            //return $response->getBody(); //json_decode($response->getBody(), true);

        } catch (\Exception $e) {
            return false;
        }

    }




    ///////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// Layout Methods /////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////
    private function processPayment($type, $trandataEncrypted, $trackId)
    {
        try {

            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
            ]);

            $response = $client->post($this->configration->keys['url'].$type, [
                'json' => [

                    [

                        //Mandatory Parameters
                        "id" => $this->configration->keys['tranportal_id'],
                        "trandata" => $trandataEncrypted,
                        "responseURL" => route('paymentResultSuccess'),
                        "errorURL" => route('paymentResultFaild')

                    ]


                ]
            ]);

            $result = json_decode($response->getBody(), true);
            $result[0]['trackId'] = $trackId;
            return $result;

        } catch (\Exception $e) {
            return false;
        }

    }





    ///////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// Assets Methods /////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////
    public function encryptAES($str,$key)
    {
        $str = $this->pkcs5_pad($str);
        $ivlen = openssl_cipher_iv_length($cipher="aes-256-cbc");
        $iv="PGKEYENCDECIVSPC";
        $encrypted = openssl_encrypt($str, "aes-256-cbc",$key, OPENSSL_ZERO_PADDING,$iv);
        $encrypted = base64_decode($encrypted);
        $encrypted = unpack('C*', ($encrypted));
        $encrypted=$this->byteArray2Hex($encrypted);
        $encrypted = urlencode($encrypted);
        return $encrypted;
    }

    function decryptAES($code, $key)
    {
        $code = $this->hex2ByteArray(trim($code));
        $code = $this->byteArray2String($code);
        $iv = "PGKEYENCDECIVSPC";
        $code = base64_encode($code);
        $decrypted = openssl_decrypt($code, 'AES-256-CBC', $key, OPENSSL_ZERO_PADDING, $iv);
        return $this->pkcs5_unpad($decrypted);
    }

    function pkcs5_pad($text)
    {
        $blocksize = 16; // AES block size is 16 bytes
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    public function byteArray2Hex($byteArray) {
        $hexString = '';
        foreach ($byteArray as $byte) {
            $hexString .= str_pad(dechex($byte), 2, '0', STR_PAD_LEFT);
        }
        return $hexString;
    }

    public function pkcs5_unpad($text)
    {
        $pad = ord($text[strlen($text) - 1]);
        return substr($text, 0, -$pad);
    }


    public function hex2ByteArray($hexString)
    {
        $byteArray = [];
        for ($i = 0; $i < strlen($hexString); $i += 2) {
            $byteArray[] = hexdec(substr($hexString, $i, 2));
        }
        return $byteArray;
    }

    public function byteArray2String($byteArray)
    {
        $string = '';
        foreach ($byteArray as $byte) {
            $string .= chr($byte);
        }
        return $string;
    }


}
