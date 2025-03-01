<?php

namespace App\Controller;

use App\Service\StatistiqueMetier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/equipements')]
class EquipementStatistiqueController extends AbstractController
{
    private StatistiqueMetier $statistiqueMetier;

    public function __construct(StatistiqueMetier $statistiqueMetier)
    {
        $this->statistiqueMetier = $statistiqueMetier;
    }

    #[Route('/statistiques', name: 'api_equipement_statistiques', methods: ['GET'])]
    public function getStatistiques(): JsonResponse
    {
        $stats = $this->statistiqueMetier->getEquipementStatistiques();
        return $this->json($stats);
    }

    // ✅ Ajout de la route pour afficher la page statistiques.html.twig
    #[Route('/statistiques-page', name: 'statistiques_equipements', methods: ['GET'])]
    public function statistiques(): Response
    {
        return $this->render('statistiques/statistiques.html.twig');
    }
}
