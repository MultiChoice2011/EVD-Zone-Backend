<?php

namespace App\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Sent Notification message throw sms
     *
     * @param array $payload
     * @return void
     */
    public function toSms(array $payload) : void
    {
        $user = $payload["user"];
        $message = $payload["message"];

        $smsPayload = [
            "userName" => config("msegat.userName"),
            "numbers" => $user->phone,
            "userSender" => config("msegat.userSender"),
            "apiKey" => config("msegat.apiKey"),
            "msg" => $payload["message"]
        ];

        $response = null;
        if (App::environment("production") || App::environment("staging")) {
            $response = Http::withHeaders([
                "Content-Type" => "application/json"
            ])->timeout(60)
            ->post(config("msegat.apiUrl"), $smsPayload);
        }

        Log::channel('sms')->info("Sending SMS", [
            "service_type" => "SMS",
            "gateway_called" => !is_null($response),
            "user" => $user->toArray(),
            "message" => $message,
            "response" => $response ? $response->json() : [],
            "datetime" => now()
        ]);
    }

    /**
     * @param string $email
     * @param Mailable $mail
     * @return void
     */
    public function toMail(string $email, Mailable $mail) : void
    {
        // TODO: must be implemented as queued job
        Mail::to($email)->send($mail);
        Log::channel('email')->info("Sending Email", [
            "service_type" => "Email",
            "email" => $email,
            "view_data" => $mail->viewData,
            "datetime" => now()
        ]);
    }
}
