<?php

namespace App\Service;

use MailchimpMarketing\ApiClient;
use Psr\Log\LoggerInterface;

class MailchimpService
{
    private ApiClient $mailchimp;
    public function __construct(string $apiKey, LoggerInterface $logger = null)
    {
        $this->mailchimp = new ApiClient();
        $this->mailchimp->setConfig([
            'apiKey' => $apiKey,
            'server' => explode('-', $apiKey)[1],
        ]);
        $this->logger = $logger;
    }

    public function ping(): array
    {
        $response = $this->mailchimp->ping->get();
        return (array) $response; // Cast stdClass to array
    }

    public function addSubscriber(string $listId, array $subscriber): array
    {
        if (!filter_var($subscriber['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException('Invalid email address: ' . $subscriber['email']);
        }
    
        try {
            $response = $this->mailchimp->lists->addListMember($listId, [
                'email_address' => $subscriber['email'],
                'status' => 'subscribed',
                'merge_fields' => [
                    'FNAME' => $subscriber['firstName'] ?? '',
                    'LNAME' => $subscriber['lastName'] ?? '',
                ],
                'status_if_new' => $subscriber['status_if_new'] ?? 'subscribed',
            ]);
            if ($this->logger) {
                $this->logger->info('Mailchimp response', ['response' => $response]);
            }
            return (array) $response;
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Mailchimp API Error', ['message' => $e->getMessage()]);
            }
            throw new \RuntimeException('Mailchimp API Error: ' . $e->getMessage());
        }
    }
}