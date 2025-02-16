<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/utilisateur')]
final class UtilisateurController extends AbstractController
{
    #[Route(name: 'app_utilisateur_index', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }


#[Route('/new', name: 'app_utilisateur_new', methods: ['GET', 'POST'])]
 public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
{
    $utilisateur = new Utilisateur();
    $form = $this->createForm(UtilisateurType::class, $utilisateur);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $hashedPassword = $passwordHasher->hashPassword($utilisateur, $utilisateur->getMotdepasse());
        $utilisateur->setPassword($hashedPassword);

        $role = $form->get('role')->getData();
        $utilisateur->setRoles([$role]);

        if ($role === 'patient') {
            $utilisateur->setAntecedentsMedicaux($form->get('antecedentsMedicaux')->getData());
            
        } elseif ($role === 'medecin') {
            $utilisateur->setSpecialite($form->get('specialite')->getData());
            $utilisateur->setHopital($form->get('hopital')->getData());
            $utilisateur->setDisponibilite($form->get('disponibilite')->getData());
        } 
        $entityManager->persist($utilisateur);
        $entityManager->flush();

        return $this->redirectToRoute('app_login');
    }

    return $this->render('utilisateur/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

    #[Route('/{id}', name: 'app_utilisateur_show', methods: ['GET'])]
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
    
            $newPassword = $form->get('motdepasse')->getData();
            if (!empty($newPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($utilisateur, $newPassword);
                $utilisateur->setPassword($hashedPassword);
            }
    
            $role = $form->get('role')->getData();
            $utilisateur->setRole($role);
    
            if ($role === 'patient') {
                $utilisateur->setAntecedentsMedicaux($form->get('antecedentsMedicaux')->getData());

            } elseif ($role === 'medecin') {
                $utilisateur->setSpecialite($form->get('specialite')->getData());
                $utilisateur->setHopital($form->get('hopital')->getData());
                $utilisateur->setDisponibilite($form->get('disponibilite')->getData());
            }
    
            $entityManager->flush();

            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateur_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
    if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->get('_token'))) {

        $entityManager->remove($utilisateur);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

    
}
