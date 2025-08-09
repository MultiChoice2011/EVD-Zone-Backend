<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class EncryptResponse
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request)->original;
        Log::info($response);

        $keyString = "123456";
        $ivString = "abc";

        // Pad key to the appropriate length (32 bytes for AES-256)
        $key = str_pad($keyString, 32);
        // Pad IV to the appropriate length (16 bytes for AES)
        $iv = str_pad($ivString, 16);
        $cipher = "aes-256-cbc"; // AES encryption algorithm with CBC mode
        // Encode the request content as JSON
        $requestData = json_encode($response);
        // Encrypt the content
        $encrypted = openssl_encrypt($requestData, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        // Check if encryption was successful
        if ($encrypted === false) {
            return response()->json(['error' => 'Encryption failed'], 500);
        }

        return Response::make(base64_encode($encrypted))->header('Content-Type', 'text/plain');

    }
}
