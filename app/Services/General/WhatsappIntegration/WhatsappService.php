<?php
namespace App\Services\General\WhatsappIntegration;
use App\Services\BaseService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class WhatsappService extends BaseService
{
    protected $client;
    protected $apiUrl;
    protected $apiKey;
    public function __construct()
    {
        $this->client = new Client();
        $this->apiUrl = config('services.whatsapp.api_url');
        $this->apiKey = config('services.whatsapp.api_key');
    }
    public function sendMessage($phoneNumber,$message){
        try {
            $response = $this->client->post($this->apiUrl, [
                'headers' => [
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
                'json' => [
                    'number'  => $phoneNumber,
                    'message' => $message,
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);

            if (isset($responseBody['status']) && $responseBody['status'] === 'success') {
                Log::info('WhatsApp message sent successfully', [
                    'phone_number' => $phoneNumber,
                    'message'      => $message,
                    'response'     => $responseBody,
                ]);
                return [
                    'success' => true,
                    'data'    => $responseBody,
                ];
            }

            // Handle non-success responses
            Log::error('Failed to send WhatsApp message', [
                'phone_number' => $phoneNumber,
                'message'      => $message,
                'response'     => $responseBody,
            ]);
            return [
                'success' => false,
                'error'   => $responseBody['error'] ?? 'An unknown error occurred',
            ];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->logException($e);
            // Handle Guzzle request errors
            Log::error('WhatsApp API request failed', [
                'phone_number' => $phoneNumber,
                'message'      => $message,
                'error'        => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            // Handle general exceptions
            Log::error('Unexpected error sending WhatsApp message', [
                'phone_number' => $phoneNumber,
                'message'      => $message,
                'error'        => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }
    // public function sendMessage($phoneNumber, $templateName, $parameters = [])
    // {
    //     $payload = [
    //         'inbox_id' => $this->inboxId,
    //         'contact' => [
    //             'phone_number' => $phoneNumber,
    //         ],
    //         'message' => [
    //             'template' => [
    //                 'name' => $templateName,
    //                 'language' => 'AR',
    //                 'parameters' => [
    //                     'body' => $parameters, // Dynamic parameters for the body placeholders
    //                 ],
    //             ],
    //         ],
    //     ];

    //     $response = Http::withHeaders([
    //         'api_account_id' => $this->accountId,
    //         'api_access_token' => $this->accessToken,
    //     ])->post($this->apiUrl, $payload);

    //     if ($response->successful()) {
    //         Log::info('WhatsApp message sent successfully', [
    //             'phone_number' => $phoneNumber,
    //             'template' => $templateName,
    //             'parameters' => $parameters,
    //             'response' => $response->json(),
    //         ]);
    //         return $response->json();
    //     } else {
    //         Log::error('Error sending WhatsApp message', [
    //             'error' => $response->json('error') ?? 'An error occurred',
    //             'status' => $response->status()
    //         ]);
    //         return [
    //             'error' => $response->json('error') ?? 'An error occurred',
    //             'status' => $response->status(),
    //         ];
    //     }
    // }
}
