<?php

namespace App\Controller;

use App\Repository\MedicamentRepository; // Assurez-vous que vous avez bien importé le bon repository
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MedicController extends AbstractController
{
    #[Route('/medic', name: 'app_medic', methods: ['GET'])]
    public function index(MedicamentRepository $medicamentRepository): Response
    {
        return $this->render('medic/index.html.twig', [
            'medicaments' => $medicamentRepository->findAll(),
        ]);
    }
}
