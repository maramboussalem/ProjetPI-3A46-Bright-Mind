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

#[Route('/parametres/viteaux')]
final class ParametresViteauxController extends AbstractController
{
    #[Route(name: 'app_parametres_viteaux_index', methods: ['GET'])]
    public function index(ParametresViteauxRepository $parametresViteauxRepository, Request $request): Response
    {
        $user = $this->getUser();

        // Get filter parameters from query
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

        $parametresViteauxes = $parametresViteauxRepository->findByFilters($user, $filters);

        return $this->render('parametres_viteaux/index.html.twig', [
            'parametres_viteauxes' => $parametresViteauxes,
        ]);
    }

   
    #[Route('/PV', name: 'app_parametres_viteaux_indexM', methods: ['GET'])]
    public function indexM(ParametresViteauxRepository $parametresViteauxRepository): Response
    {
        return $this->render('parametres_viteaux/indexM.html.twig', [
            'parametres_viteauxes' => $parametresViteauxRepository->findAll(),
        ]);
    }


    #[Route('/new', name: 'app_parametres_viteaux_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $parametresViteaux = new ParametresViteaux();
    
        // Associer l'utilisateur connecté aux nouveaux paramètres vitaux
        $parametresViteaux->setUser($this->getUser()); // Ajoute l'utilisateur connecté

        $form = $this->createForm(ParametresViteauxType::class, $parametresViteaux);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($parametresViteaux);
                $entityManager->flush();

                $this->addFlash('success', 'Paramètres vitaux ajoutés avec succès.');

                return $this->redirectToRoute('app_parametres_viteaux_index');
            }

            $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
        }

        return $this->render('parametres_viteaux/new.html.twig', [
            'parametres_viteaux' => $parametresViteaux,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_parametres_viteaux_show', methods: ['GET'])]
    public function show(ParametresViteaux $parametresViteaux): Response
    {
        // Vérifier que l'utilisateur connecté possède ces paramètres vitaux
        if ($parametresViteaux->getUser() !== $this->getUser()) {
            if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_MEDECIN')) {
                throw $this->createAccessDeniedException('Vous n\'avez pas accès à ces paramètres vitaux.');
            }
        }

        return $this->render('parametres_viteaux/show.html.twig', [
            'parametres_viteaux' => $parametresViteaux,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parametres_viteaux_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ParametresViteaux $parametresViteaux, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParametresViteauxType::class, $parametresViteaux);
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
        $token = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $parametresViteaux->getId(), $token)) {
            $entityManager->remove($parametresViteaux);
            $entityManager->flush();
            $this->addFlash('success', 'Paramètres vitaux supprimés avec succès.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide, suppression annulée.');
        }

        return $this->redirectToRoute('app_parametres_viteaux_index');
    }
}
