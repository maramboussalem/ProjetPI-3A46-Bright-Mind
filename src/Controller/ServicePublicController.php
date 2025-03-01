<?php

namespace App\Controller;

use App\Repository\ServiceMedRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\SynVisionChatbotService;
use Symfony\Component\HttpFoundation\JsonResponse;
final class ServicePublicController extends AbstractController
{
    #[Route('/service/public', name: 'app_service_public', methods: ['GET'])]
    public function index(ServiceMedRepository $serviceMedRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Récupération des services médicaux avec QueryBuilder
        $query = $serviceMedRepository->createQueryBuilder('s')->getQuery();

        // Pagination avec KNP Paginator
        $pagination = $paginator->paginate(
            $query,  
            $request->query->getInt('page', 1), // Page courante
            3 // Nombre d'éléments par page
        );

        return $this->render('service_public/index.html.twig', [
            'service_meds' => $pagination, // Utilisation correcte de la pagination
        ]);
    }
  
}