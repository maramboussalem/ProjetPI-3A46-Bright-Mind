<?php

namespace App\Controller;

use App\Entity\Fournisseur;
use App\Form\FournisseurType;
use App\Repository\FournisseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/fournisseur')]
final class FournisseurController extends AbstractController
{
    #[Route(name: 'app_fournisseur_index', methods: ['GET'])]
    public function index(FournisseurRepository $fournisseurRepository , Request $request , PaginatorInterface $paginator): Response
    {

        $pagination = $paginator->paginate(
            $fournisseurRepository->findAll(),
            $request->query->getInt('page', 1),
            2 // items per page
        );

        return $this->render('fournisseur/index.html.twig', [
            'fournisseurs' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_fournisseur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FournisseurRepository $fournisseurRepository, ValidatorInterface $validator): Response
    {
        $fournisseur = new Fournisseur();
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si un fournisseur avec le même email ou nom existe déjà
            $existingFournisseur = $fournisseurRepository->findOneBy([
                'NomFournisseur' => $fournisseur->getNomFournisseur(),
                'Email' => $fournisseur->getEmail(),
            ]);

            if ($existingFournisseur) {
                $this->addFlash('error', 'Un fournisseur avec ces informations existe déjà.');
                return $this->render('fournisseur/new.html.twig', [
                    'fournisseur' => $fournisseur,
                    'form' => $form,
                ]);
            }

            // Validation Symfony
            $errors = $validator->validate($fournisseur);
            if (count($errors) > 0) {
                return $this->render('fournisseur/new.html.twig', [
                    'fournisseur' => $fournisseur,
                    'form' => $form,
                    'errors' => $errors,
                ]);
            }

            $entityManager->persist($fournisseur);
            $entityManager->flush();

            return $this->redirectToRoute('app_fournisseur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fournisseur/new.html.twig', [
            'fournisseur' => $fournisseur,
            'form' => $form,
            'errors' => [],
        ]);
    }

    #[Route('/{id}', name: 'app_fournisseur_show', methods: ['GET'])]
    public function show(Fournisseur $fournisseur): Response
    {
        return $this->render('fournisseur/show.html.twig', [
            'fournisseur' => $fournisseur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fournisseur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fournisseur $fournisseur, EntityManagerInterface $entityManager, FournisseurRepository $fournisseurRepository): Response
    {
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si un autre fournisseur a les mêmes informations
            $existingFournisseur = $fournisseurRepository->findOneBy([
                'NomFournisseur' => $fournisseur->getNomFournisseur(),
                'Email' => $fournisseur->getEmail(),
            ]);

            if ($existingFournisseur && $existingFournisseur->getId() !== $fournisseur->getId()) {
                $this->addFlash('error', 'Un autre fournisseur avec ces informations existe déjà.');
                return $this->render('fournisseur/edit.html.twig', [
                    'fournisseur' => $fournisseur,
                    'form' => $form,
                ]);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_fournisseur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fournisseur/edit.html.twig', [
            'fournisseur' => $fournisseur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fournisseur_delete', methods: ['POST'])]
    public function delete(Request $request, Fournisseur $fournisseur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $fournisseur->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($fournisseur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_fournisseur_index', [], Response::HTTP_SEE_OTHER);
    }
}
