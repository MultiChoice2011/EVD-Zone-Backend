<?php

use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;


if(!function_exists('generateRandomString'))
{
    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++)
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
// if(!function_exists('routeIndex'))
// {
//     /**
//      * Generate a route name for the previous request.
//      *
//      * @return string|null
//      */
//     function routeIndex()
//     {
//         $url = explode("admin/", url()->current());
//         $url = url("/") . "/admin/" . explode("/", $url[1])[0];
//         $currentRequest = app('request')->create($url);
//         // try {
//         $routeName = app('router')->getRoutes()->match($currentRequest)->getName();
//         // } catch (NotFoundHttpException $exception) {
//         //     return ['routeName' => '', 'routeUrl' => $url];
//         // }
//         return ['routeName' => $routeName, 'routeUrl' => $url];
//     }
// }
//TODO: this function need to replaced with something more accurate and implement good performance

//TODO: this function need to replaced with something more accurate and implement good performance
if(!function_exists('transactionCode'))
{
    function transactionCode(): string
    {
        do {
            $code = rand(111111, 999999) . substr(time(), -6);
        } while (Transaction::where("code", "=", $code)->first());

        return $code;
    }
}
if(!function_exists('amountInSar'))
{
    function amountInSar(float $amount): float
    {
        return $amount / 100;
    }
}
if(!function_exists('amountInHalala'))
{
    function amountInHalala(float $amountSar): float
    {
        return $amountSar * 100;
    }
}
if(!function_exists('amountInSarRounded'))
{
    function amountInSarRounded(float $amount): string
    {
        return number_format($amount / 100, 2);
    }
}

if(!function_exists('isAPiInternational'))
{
    function isAPiInternational(): bool
    {
        return Str::contains(Route::current()?->uri() ?? "", "api") &&
            request()->hasHeader(CustomHeaders::COUNTRY_CODE);
    }
}

// this helper is helpful when dealing with match between 2 arrays nested like: comparing translation files from 2 branches
if (!function_exists("arrayKeysRecursive")) {
    function arrayKeysRecursive(array $array, $devider='.'){
        $arrayKeys = [];
        foreach( $array as $key=>$value ){
            if( is_array($value) ){
                $rekusiveKeys = arrayKeysRecursive($value, $devider);
                foreach( $rekusiveKeys as $rekursiveKey ){
                    $arrayKeys[] = $key.$devider.$rekursiveKey;
                }
            }else{
                $arrayKeys[] = $key;
            }
        }
        return $arrayKeys;
    }
}
if (!function_exists("customNumberFormat")) {
    function customNumberFormat($number, $decimals = 0, $decPoint = '.' , $thousandsSep = ',')
    {
        $negation = ($number < 0) ? (-1) : 1;
        $coefficient = 10 ** $decimals;
        $number = $negation * floor((string)(abs($number) * $coefficient)) / $coefficient;
        return number_format($number, $decimals, $decPoint, $thousandsSep);
    }
}

if (!function_exists("textIsEnglish")) {
    function textIsEnglish(string $text): bool {
        return preg_match("/^[a-zA-Z1-9 -_]+$/", $text);
    }
}
