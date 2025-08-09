<?php

namespace App\Services\General\PaymentGateways;

use App\Services\General\CurrencyService;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HyperpayService
{

    public function __construct()
    {

    }

    public function initiatePayment(array $paymentData)
    {
        // Determine entityId based on the payment method
        $entityId = $this->getEntityId($paymentData['payment_method']);

        // Prepare request data
        $data = $this->prepareRequestData($paymentData, $entityId);

        // Send the request to Hyperpay
        $response = $this->sendPaymentRequest($data);
        Log::info($response);

        // Return checkout_id if success
        $responseBody = $response->json();
        if ($response->successful() && isset($responseBody['id'])) {
            $data = [
                'checkout_id' => $responseBody['id'],
                'entity_id' => $entityId,
            ];
            return $data;
        }else{
            return false;
        }

    }

    /**
     * Get the entity ID based on the payment method.
     *
     * @param string $paymentMethod
     * @return string
     */
    private function getEntityId(string $paymentMethod): string
    {
        return match($paymentMethod) {
            'MADA' => config('services.hyperpay.entity_id_mada'),
            'STCPay' => config('services.hyperpay.entity_id_mada'),
            default => config('services.hyperpay.entity_id_visa_mastercard'),
        };
    }

    /**
     * Prepare request data for the Hyperpay API.
     *
     * @param array $validated
     * @param string $entityId
     * @return array
     */
    private function prepareRequestData(array $validated, string $entityId): array
    {
        $authCustomer = Auth::guard('customerApi')->user();
        $currentCurrency = CurrencyService::getCurrentCurrency($authCustomer);
        $data = [
            'entityId' => $entityId,
            'amount' => number_format(($validated['amount'] / $currentCurrency->value), 2, '.', ''),
            'currency' => config('services.hyperpay.currency'),
            'paymentType' => config('services.hyperpay.payment_type'),
            'merchantTransactionId' => uniqid(),
            'customer.email' => $validated['customer_email'],
            'customer.givenName' => $validated['customer_given_name'],
            'customer.surname' => $validated['customer_surname'],
            'billing.street1' => $validated['billing_street1'] ?? 'King Abdulaziz Road',
            'billing.city' => $validated['billing_city'] ?? 'Riyadh',
            'billing.state' => $validated['billing_state'] ?? 'Riyadh Region',
            'billing.country' => $validated['billing_country'] ?? 'SA',
            'billing.postcode' => $validated['billing_postcode'] ?? '11564',
        ];

        // (if test mode is true)
        if (env('HYPERPAY_TEST_MODE')) {
            $data['amount'] = intval( number_format($validated['amount'], 2) / $currentCurrency->value );
            $data['testMode'] = 'EXTERNAL';
            $data['customParameters'] = [
                '3DS2_enrolled' => 'true'
            ];
        }
        Log::info($data);
        return $data;
    }

    /**
     * Send the payment request to Hyperpay.
     *
     * @param array $data
     * @return \Illuminate\Http\Client\Response
     */
    private function sendPaymentRequest(array $data)
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.hyperpay.access_token'),
        ])->asForm()->post(config('services.hyperpay.base_url') . 'v1/checkouts',$data);
    }

    public function payment($data)
    {
        try {

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.hyperpay.access_token'),
            ])->get(config('services.hyperpay.base_url') . 'v1/checkouts/'. $data['id'] .'/payment', [
                'entityId' => $data['entity_id'],
            ]);
            Log::info($response->successful());
            Log::info($response);
            $responseBody = $response->json();
            //dd($responseBody);

            //return $responseBody;
            if ($response->successful() && (/*$responseBody['result']['code'] == '000.100.110' ||*/ $responseBody['result']['code'] == '000.000.000')) {
                $data = [
                    'reference_number' => $responseBody['merchantTransactionId'],
                    'payment_id' => $responseBody['id'],
                    'payment_type' => 'Hyperpay',
                    'amount' => $responseBody['amount'],
                    'currency' => $responseBody['currency'],
                    'paymentBrand' => $responseBody['paymentBrand'],
                    'last_4_digits' => $responseBody['card']['last4Digits'],
                ];
                return $data;
            }else{
                return false;
            }

        } catch (\Exception $e) {
            Log::info($e);
            return $e->getMessage();
        }

    }





}
