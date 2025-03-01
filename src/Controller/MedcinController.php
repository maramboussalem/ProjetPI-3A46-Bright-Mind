<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MedcinController extends AbstractController
{
    #[Route('/medcin', name: 'app_medcin')]
    public function index(): Response
    {
        return $this->render('medcin/index.html.twig', [
            'controller_name' => 'MedcinController',
        ]);
    }
}
