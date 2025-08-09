<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DecryptRequest
{
    // Define your decryption key
    private $decryptionKey = '123456';
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $keyString = "123456";
        $ivString = "abc";

        // Pad key to the appropriate length (32 bytes for AES-256)
        $key = str_pad($keyString, 32);
        // Pad IV to the appropriate length (16 bytes for AES)
        $iv = str_pad($ivString, 16);
        $cipher = "aes-256-cbc"; // AES encryption algorithm with CBC mode
        // Decode the base64-encoded content before decrypting
        $encryptedData = base64_decode($request->getContent());
        // Decrypt the content
        $decrypted = openssl_decrypt($encryptedData, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        // Check if decryption was successful
        if ($decrypted === false) {
            return response()->json(['error' => 'Decryption failed'], 400);
        }
        // Convert decrypted JSON data to array and replace request content
        $request->replace(json_decode($decrypted, true));

        return $next($request);
    }
}
