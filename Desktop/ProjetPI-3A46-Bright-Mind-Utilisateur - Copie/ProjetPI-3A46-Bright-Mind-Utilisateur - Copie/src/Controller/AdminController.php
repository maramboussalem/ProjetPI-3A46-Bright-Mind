<?php

namespace App\Controller;

use App\Repository\EquipementRepository;
use App\Repository\ServiceMedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(EquipementRepository $equipementRepository, ServiceMedRepository $serviceMedRepository): Response
    {
        // Récupérer les équipements et services médicaux depuis la base de données
        $equipements = $equipementRepository->findAll();
        $services = $serviceMedRepository->findAll();

        return $this->render('admin/index_back.html.twig', [
            'equipements' => $equipements,
            'services' => $services,
        ]);
    }
}
