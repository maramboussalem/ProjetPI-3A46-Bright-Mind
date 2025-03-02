<?php

namespace App\Controller;

use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{
    #[Route('/send-mail', name: 'send_mail')]
    public function sendMail(EmailService $emailService): Response
    {
        $emailService->sendEmail(
            'destinataire@example.com', // Adresse email du destinataire
            'Sujet de l\'email',
            'Bonjour, ceci est un test d\'email avec Symfony 6.4 !'
        );

        return new Response('Email envoyé avec succès !');
    }
}