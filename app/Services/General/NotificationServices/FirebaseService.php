<?php

namespace App\Services\General\NotificationServices;

use Exception;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;

class FirebaseService
{
    private $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(config('firebase.projects.app.credentials'));
        $this->messaging = $factory->createMessaging();
    }

    public function sendNotification($topic, $title, $body, $data = [])
    {
        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification(['title' => $title, 'body' => $body])
            ->withData($data)
            ->withHighestPossiblePriority()
            ->withDefaultSounds()
            ->withApnsConfig(
                ApnsConfig::new()
                    ->withDefaultSound()
                    ->withImmediatePriority()
            );

        $result = $this->messaging->send($message);
        Log::info($result);
        return $result;
    }

    public function subscribeToTopics($token, $topics = ['all-customers'])
    {
        $result = $this->messaging->subscribeToTopics($topics, $token);
        Log::info($result);
        return $result;
    }

    public function unsubscribeFromTopic($token, $topic)
    {
        $result = $this->messaging->unsubscribeFromTopic($topic, $token);
        Log::info($result);
        return $result;
    }

    public function validateRegistrationTokens($tokens)
    {
        $result = $this->messaging->validateRegistrationTokens($tokens);
        Log::info($result);
        return $result;
    }





}
