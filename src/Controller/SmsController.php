<?php

namespace App\Controller;

use App\Service\TwilioSmsService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SmsController extends AbstractController
{
    #[Route('/send-sms', name: 'send_sms')]
    public function sendSms(TwilioSmsService $smsService): Response
    {
        $recipient = '+21655388787'; // Remplacez par le numéro du destinataire
        $message = 'Bonjour, Maraaaam!';

        try {
            $smsService->sendSms($recipient, $message);
            return new Response('SMS envoyé avec succès !');
        } catch (\Exception $e) {
            return new Response('Erreur lors de l\'envoi du SMS : ' . $e->getMessage());
        }
    }
}
