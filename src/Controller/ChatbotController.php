<?php

namespace App\Controller;

use App\Service\ChatbotService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

class ChatbotController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/chatbot', name: 'chatbot')]
    public function chat(Request $request, ChatbotService $chatbotService): JsonResponse
    {
        $userMessage = $request->query->get('message', 'Bonjour !');
        $this->logger->info($userMessage);
        $botResponse = $chatbotService->askChatbot($userMessage);

        return $this->json(['response' => $botResponse]);
    }

    #[Route('/chat', name: 'chat')]
    public function index()
    {
        return $this->render('chat/index.html.twig');
    }
}
