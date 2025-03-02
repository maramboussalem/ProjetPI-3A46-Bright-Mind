<?php

namespace App\Service;

use Google\Client;
use Google\Service\Gmail;

class GoogleClientService
{
    public function getClient(): Client
    {
        $client = new Client();
        $client->setApplicationName('SymfonyMailerAPI');
        $client->setAuthConfig(__DIR__ . '/../../config/credentials.json');
        $client->setScopes([
            Gmail::MAIL_GOOGLE_COM, // Permission pour envoyer des emails
        ]);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        return $client;
    }
}
