<?php

namespace App\Controller;

use App\Entity\ParametresViteaux;
use App\Form\ParametresViteauxType;
use App\Repository\ParametresViteauxRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;


final class DiagnosticPageController extends AbstractController
{
    #[Route('/diag/page', name: 'app_diagnostic_page', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        Security $security, 
        EntityManagerInterface $entityManager, 
        ParametresViteauxRepository $parametresViteauxRepository
    ): Response {
        // Create the form
        $user = $security->getUser();
        $parametresViteaux = new ParametresViteaux();
        $parametresViteaux->setUser($user);
        $form = $this->createForm(ParametresViteauxType::class, $parametresViteaux);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Persist the entity if valid
                $entityManager->persist($parametresViteaux);
                $entityManager->flush();
                
                // Success message
                $this->addFlash('success', 'Paramètres vitaux ajoutés avec succès.');

                return $this->redirectToRoute('app_diagnostic_page');
            } else {
                // Error message
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');

                // Add detailed validation errors
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
        }

        // Fetch existing records
        $parametresViteauxes = $parametresViteauxRepository->findAll();

        return $this->render('diagnostic_page/index.html.twig', [
            'parametres_viteauxes' => $parametresViteauxes,
            'form' => $form->createView(),
        ]);
    }
}
