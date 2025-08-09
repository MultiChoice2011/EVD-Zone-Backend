<?php

namespace App\Services\General;

use App\Models\Currency;

class CurrencyService
{
    public static function getCurrentCurrency($authSeller = null)
    {
        // Retrieve the current currency code from session or default to 'USD'
        if (is_object($authSeller) && $authSeller->currency) {
            return $authSeller->currency;
        }

        $currencyCode = session('currency', null);
        // Retrieve the currency value from session or the default currency if session is not set
        if ($currencyCode) {
            $currency = Currency::where('code', $currencyCode)->first();
        }
        if (!isset($currency) || !$currency) {
            $currency = Currency::where('is_default', true)->first();
        }

        return $currency;
    }
}
