<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;

class GithubController extends AbstractController
{
    #[Route('/connect/github', name: 'connect_github_start')]
    public function connectToGithub(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry->getClient('github')->redirect(['read:user', 'user:email']);
    }

    #[Route('/connect/github/check', name: 'connect_github_check')]
    public function connectCheck(): void
    {
        // L'authenticator gère cette partie
    }
}