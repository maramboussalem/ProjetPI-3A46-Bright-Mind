<?php

namespace App\Controller;

use App\Service\CaptchaGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CaptchaController extends AbstractController
{
    #[Route('/captcha/generate', name: 'app_captcha_generate')]
    public function generateCaptcha(CaptchaGenerator $captchaGenerator, SessionInterface $session): Response
{
    // Générer le CAPTCHA et récupérer l'image
    $imageResponse = $captchaGenerator->generateCaptcha();

    // Sauvegarder le texte du CAPTCHA dans la session
    $captchaText = $_SESSION['captcha'] ?? 'Aucun texte trouvé';
    $session->set('captcha_text', $captchaText);

    // Retourner l'image en réponse avec le bon type MIME
    return new Response(
        $imageResponse->getContent(),
        Response::HTTP_OK,
        ['Content-Type' => 'image/png']
    );
}

}
