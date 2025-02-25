<?php
namespace App\Service;

use Twilio\Rest\Client;

class TwilioSmsService
{
    private string $sid;
    private string $authToken;
    private string $twilioPhoneNumber;

    public function __construct(string $sid, string $authToken, string $twilioPhoneNumber)
    {
        $this->sid = $sid;
        $this->authToken = $authToken;
        $this->twilioPhoneNumber = $twilioPhoneNumber;
    }

    public function sendSms(string $recipientNumber, string $message): void
    {
        $client = new Client($this->sid, $this->authToken);

        $client->messages->create(
            $recipientNumber, // Numéro du destinataire
            [
                'from' => $this->twilioPhoneNumber, // Numéro Twilio
                'body' => $message // Contenu du SMS
            ]
        );
    }
}
