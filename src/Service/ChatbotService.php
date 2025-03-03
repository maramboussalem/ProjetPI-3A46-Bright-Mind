<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Psr\Log\LoggerInterface;

class ChatbotService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $httpClient, string $apiKey, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->logger = $logger;
    }

    public function askChatbot(string $message): string
    {
        try {
            $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
            
            $response = $this->httpClient->request('POST', $endpoint, [
                'query' => [
                    'key' => $this->apiKey
                ],
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $message]
                            ],
                        ],
                    ]
                ]
            ]);

            try {
                $statusCode = $response->getStatusCode();
                $content = $response->getContent();
            } catch (TransportExceptionInterface $e) {
                $this->logger->error('Transport error', ['error' => $e->getMessage()]);
                return 'Erreur de communication avec l\'API';
            }
            
            $this->logger->debug('Gemini API response', [
                'status' => $statusCode,
                'response' => $content
            ]);

            if ($statusCode !== 200) {
                $this->logger->error('API error', [
                    'status' => $statusCode,
                    'response' => $content
                ]);
                return 'Erreur: ' . $content;
            }

            $responseData = json_decode($content, true);
            if (!is_array($responseData)) {
                return 'Erreur: Réponse invalide';
            }

            return $responseData['candidates'][0]['content']['parts'][0]['text'] ?? 'Pas de réponse';

        } catch (\Exception $e) {
            $this->logger->error('Exception', ['error' => $e->getMessage()]);
            return 'Erreur: ' . $e->getMessage();
        }
    }
}
