<?php

namespace App\Controller;

use App\Entity\Medicament;
use App\Form\MedicamentType;
use App\Repository\MedicamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/medicament2')] // La route est modifiée pour être distincte
final class Medicament2Controller extends AbstractController
{
    // Index Route - List all medicaments
    #[Route('/', name: 'app_medicament2_index2', methods: ['GET'])]
    public function index(MedicamentRepository $medicamentRepository): Response
    {
        return $this->render('medicament/index2.html.twig', [
            'medicaments' => $medicamentRepository->findAll(),
        ]);
    }

}

