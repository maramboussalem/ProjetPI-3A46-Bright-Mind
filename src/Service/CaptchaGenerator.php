<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class CaptchaGenerator
{
    public function generateCaptcha(): Response
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $width = 140;
        $height = 60;

        // Créer une image vide
        $image = imagecreate($width, $height);

        // Définir des couleurs
        $backgroundColor = imagecolorallocate($image, 255, 255, 255); // Blanc
        $textColor = imagecolorallocate($image, 0, 0, 0); // Noir

        // Ajouter des pixels aléatoires pour rendre le CAPTCHA plus difficile
        for ($i = 0; $i < 200; $i++) {
            $dotColor = imagecolorallocate($image, rand(100, 255), rand(100, 255), rand(100, 255));
            imagesetpixel($image, rand(0, $width), rand(0, $height), $dotColor);
        }

        // Générer un texte aléatoire pour le CAPTCHA
        $captchaText = $this->generateRandomText();

        // Ajouter du texte à l'image
        $font = 5; // Taille de la police intégrée
        $x = rand(10, 40);
        $y = rand(20, 30);
        imagestring($image, $font, $x, $y, $captchaText, $textColor);

        // Sauvegarder le texte du CAPTCHA dans la session
        $_SESSION['captcha'] = $captchaText;

        // Capturer l'image dans un tampon de sortie
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();

        // Libérer la mémoire
        imagedestroy($image);

        // Retourner l'image avec le type MIME approprié et le texte CAPTCHA dans une variable
        return new Response($imageData, Response::HTTP_OK, ['Content-Type' => 'image/png']);
    }

    private function generateRandomText($length = 5): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $randomText = '';
        for ($i = 0; $i < $length; $i++) {
            $randomText .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomText;
    }

    // Nouvelle méthode pour obtenir le texte CAPTCHA dans une variable
    public function getCaptchaText(): string
    {
        return $_SESSION['captcha'] ?? '';
    }
}
