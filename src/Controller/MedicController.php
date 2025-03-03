<?php

namespace App\Controller;

use App\Repository\FournisseurRepository;
use App\Repository\MedicamentRepository; // Assurez-vous que vous avez bien importé le bon repository
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MedicController extends AbstractController
{
    #[Route('/medic', name: 'app_medic', methods: ['GET'])]
    public function index(
        FournisseurRepository $fournisseurRepository, 
        MedicamentRepository $medicamentRepository, 
        PaginatorInterface $paginator, 
        Request $request
    ): Response {
        $searchTerm = $request->query->get('search', '');
    
        $queryBuilder = $medicamentRepository->createQueryBuilder('m')
            ->where('m.isshown = :isshown')
            ->setParameter('isshown', true);
    
        if (!empty($searchTerm)) {
            $queryBuilder->andWhere('m.NomMedicament LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }
    
        $query = $queryBuilder->getQuery();
    
        $pagination2 = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            4 // items per page
        );
    
        return $this->render('medic/index.html.twig', [
            'medicaments' => $pagination2,
            'searchTerm' => $searchTerm,
        ]);
    }
    
}
