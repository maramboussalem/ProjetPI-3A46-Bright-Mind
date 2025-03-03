<?php

namespace App\Controller;

use App\Entity\ParametresViteaux;
use App\Form\ParametresViteauxType;
use App\Repository\ParametresViteauxRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class DiagnosticPageController extends AbstractController
{
    private HttpClientInterface $httpClient;


    #[Route('/diag/page', name: 'app_diagnostic_page', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        Security $security,
        EntityManagerInterface $entityManager,
        ParametresViteauxRepository $parametresViteauxRepository
    ): Response {
        // Create the form for submitting vital parameters
        $user = $security->getUser();
        $parametresViteaux = new ParametresViteaux();
        $parametresViteaux->setUser($user);
        $form = $this->createForm(ParametresViteauxType::class, $parametresViteaux);
        $form->handleRequest($request);

        $diagnosticResult = null; // Placeholder for AI response

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist data
            $entityManager->persist($parametresViteaux);
            $entityManager->flush();


            $this->addFlash('success', 'Paramètres vitaux ajoutés avec succès.');
            return $this->redirectToRoute('app_diagnostic_page');
        }

        // Handling filter inputs
        $filters = [
            'name' => $request->query->get('name'),
            'age' => $request->query->get('age'),
            'fc' => $request->query->get('fc'),
            'fr' => $request->query->get('fr'),
            'ecg' => $request->query->get('ecg'),
            'gad' => $request->query->get('gad'),
            'tas' => $request->query->get('tas'),
            'tad' => $request->query->get('tad'),
            'spo2' => $request->query->get('spo2'),
            'gsc' => $request->query->get('gsc'),
            'date' => $request->query->get('date'),
        ];

        // Build the query with filters
        $queryBuilder = $parametresViteauxRepository->createQueryBuilder('p')
            ->where('p.user = :user')
            ->setParameter('user', $user);

        
    foreach ($filters as $key => $value) {
        if ($value) {
            if ($key === 'date') {
                $queryBuilder->andWhere("p.createdAt LIKE :date")
                            ->setParameter('date', "%$value%");
            } else {
                $queryBuilder->andWhere("p.$key LIKE :$key")
                            ->setParameter($key, "%$value%");
            }
        }
    }
        

        $parametresViteauxes = $queryBuilder->getQuery()->getResult();

        return $this->render('diagnostic_page/index.html.twig', [
            'parametres_viteauxes' => $parametresViteauxes,
            'form' => $form->createView(),
            'diagnostic' => $diagnosticResult,
        ]);
    }
}
