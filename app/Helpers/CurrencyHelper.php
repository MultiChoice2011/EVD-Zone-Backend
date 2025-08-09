<?php



function convertCurrency(float $amount, string $toCurrency, string $fromCurrency = 'SAR'): float|bool
{
    $fromCurrency = strtoupper($fromCurrency);
    $toCurrency = strtoupper($toCurrency);

    $exchangeRates = [
        'USD' => 1,
        'SAR' => 3.75,
        'EGP' => 55,
    ];

    if (!isset($exchangeRates[$fromCurrency]) || !isset($exchangeRates[$toCurrency])) {
        return false;
    }

    $amountInUSD = $amount / $exchangeRates[$fromCurrency];

    return round($amountInUSD * $exchangeRates[$toCurrency], 2);
}
