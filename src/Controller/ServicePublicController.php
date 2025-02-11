<?php

namespace App\Controller;

use App\Repository\ServiceMedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ServicePublicController extends AbstractController
{
    #[Route('/service/public', name: 'app_service_public', methods: ['GET'])]

    public function index(ServiceMedRepository $serviceMedRepository): Response
    {
        return $this->render('service_public/index.html.twig', [
            'service_meds' => $serviceMedRepository->findAll(),
        ]);
    }
}
