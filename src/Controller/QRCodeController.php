<?php

namespace App\Controller;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QRCodeController extends AbstractController
{
    #[Route('/q/r/code', name: 'app_q_r_code')]
    public function generateQRCode(): Response
    {
        // Créer un objet QrCode avec les données que vous voulez encoder
        $qrCode = new QrCode('https://www.example.com');

        // Personnalisation (facultatif)
        $qrCode->setSize(300); // Taille du QR Code
        $qrCode->setMargin(10); // Marge autour du QR Code
        
        // Créer l'image du QR Code au format PNG
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Retourner le QR Code sous forme d'image PNG
        return new Response(
            $result->getString(),
            Response::HTTP_OK,
            ['Content-Type' => 'image/png']
        );
    }

    #[Route('/q/r/code/base64', name: 'app_q_r_code_base64')]
    public function generateQRCodeBase64(): Response
    {
        // Créer un objet QrCode avec les données que vous voulez encoder
        $qrCode = new QrCode('https://www.example.com');
        $qrCode->setSize(300);
        $qrCode->setMargin(10);

        // Créer l'image du QR Code
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Générer l'URI en base64
        $dataUri = $result->getDataUri();

        // Passer la donnée à la vue Twig
        return $this->render('qr_code/index.html.twig', [
            'qr_code_data_uri' => $dataUri,
        ]);
    }
}
