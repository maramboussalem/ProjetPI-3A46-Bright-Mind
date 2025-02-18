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

#[Route('/parametres/viteaux')]
final class ParametresViteauxController extends AbstractController
{
    #[Route(name: 'app_parametres_viteaux_index', methods: ['GET'])]
    public function index(ParametresViteauxRepository $parametresViteauxRepository): Response
    {
        return $this->render('parametres_viteaux/index.html.twig', [
            'parametres_viteauxes' => $parametresViteauxRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_parametres_viteaux_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $parametresViteaux = new ParametresViteaux();
        $form = $this->createForm(ParametresViteauxType::class, $parametresViteaux);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Si le formulaire est valide, persister les données
                $entityManager->persist($parametresViteaux);
                $entityManager->flush();

                // Message de succès
                $this->addFlash('success', 'Paramètres vitaux ajoutés avec succès.');

                return $this->redirectToRoute('app_parametres_viteaux_index');
            } else {
                // Si le formulaire n'est pas valide, afficher les erreurs
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');

                // Afficher toutes les erreurs de validation du formulaire
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
        }

        return $this->render('parametres_viteaux/new.html.twig', [
            'parametres_viteaux' => $parametresViteaux,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_parametres_viteaux_show', methods: ['GET'])]
    public function show(ParametresViteaux $parametresViteaux): Response
    {
        return $this->render('parametres_viteaux/show.html.twig', [
            'parametres_viteaux' => $parametresViteaux,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parametres_viteaux_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ParametresViteaux $parametresViteaux, EntityManagerInterface $entityManager): Response
    {
        // Disable CSRF protection for this action
        $form = $this->createForm(ParametresViteauxType::class, $parametresViteaux, [
            'csrf_protection' => false, // Disable CSRF protection
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Paramètres vitaux mis à jour avec succès.');
            return $this->redirectToRoute('app_parametres_viteaux_index');
        }

        return $this->render('parametres_viteaux/edit.html.twig', [
            'parametres_viteaux' => $parametresViteaux,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_parametres_viteaux_delete', methods: ['POST'])]
    public function delete(Request $request, ParametresViteaux $parametresViteaux, EntityManagerInterface $entityManager): Response
    {
        // Disable CSRF token validation for delete action
        $token = $request->request->get('_token');
        
        // If you want to completely remove CSRF token, comment the validation line:
        if ($this->isCsrfTokenValid('delete' . $parametresViteaux->getId(), $token)) {
            $entityManager->remove($parametresViteaux);
            $entityManager->flush();
            $this->addFlash('success', 'Paramètres vitaux supprimés avec succès.');
        } else {
            // Message of error if CSRF is invalid
            $this->addFlash('error', 'Jeton CSRF invalide, suppression annulée.');
        }

        return $this->redirectToRoute('app_parametres_viteaux_index');
    }
}
