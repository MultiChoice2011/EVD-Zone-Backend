<?php

namespace App\Helpers;

use Exception;

class OpenSslHelper
{
    /**
     * @var string
     */
    private static string $secret;
    /**
     * @var string
     */
    private static string $cipher = "aes-256-cbc";

    public static function initialize()
    {
        if (!isset(self::$secret)) {
            self::$secret = config('services.enc_secret_key');
        }
    }

    /**
     * @param $data
     * @return string
     */
    public static function encrypt($data): string
    {
        self::initialize();
        $object = json_encode($data);
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($object, self::$cipher, self::$secret, OPENSSL_RAW_DATA, $iv);
        $result = base64_encode($iv . $encrypted);
        return $result;
    }

    /**
     * @param string $data
     * @return mixed
     */
    public static function decrypt(string $data): mixed
    {
        self::initialize();
        try {
            $encryptedData = base64_decode($data);
            $iv = substr($encryptedData, 0, 16);
            $encryptedPayload = substr($encryptedData, 16);
            $decrypted = openssl_decrypt($encryptedPayload, self::$cipher, self::$secret, OPENSSL_RAW_DATA, $iv);
            if ($decrypted === false) {
                return false;
            }
            return json_decode($decrypted, true);
        }
        catch (Exception $e) {
            return false;
        }
    }
}
