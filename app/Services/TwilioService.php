<?php

namespace App\Services;
use Twilio\Rest\Client;

class TwilioService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    }

    public function sendWhatsAppMessage($to, $body)
    {
        return $this->client->messages->create(
            'whatsapp:' . $to,
            [
                'from' => 'whatsapp:' . config('services.twilio.from'),
                'body' => $body
            ]
        );
    }

    public function sendSmsMessage($to, $body)
    {
        return $this->client->messages->create(
            $to,
            [
                'from' => config('services.twilio.from'),
                'body' => $body
            ]
        );
    }
}
