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

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($parametresViteaux);
            $entityManager->flush();

            return $this->redirectToRoute('app_parametres_viteaux_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parametres_viteaux/new.html.twig', [
            'parametres_viteaux' => $parametresViteaux,
            'form' => $form,
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
        $form = $this->createForm(ParametresViteauxType::class, $parametresViteaux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_parametres_viteaux_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parametres_viteaux/edit.html.twig', [
            'parametres_viteaux' => $parametresViteaux,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_parametres_viteaux_delete', methods: ['POST'])]
    public function delete(Request $request, ParametresViteaux $parametresViteaux, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$parametresViteaux->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($parametresViteaux);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_parametres_viteaux_index', [], Response::HTTP_SEE_OTHER);
    }
}
