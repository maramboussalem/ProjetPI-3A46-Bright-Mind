<?php

// src/Controller/AdminController.php
namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/index_back.html.twig');
    }

    #[Route('/create-admin', name: 'create_admin')]
    public function createAdmin(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $admin = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $admin->setMotdepasse($passwordHasher->hashPassword($admin, $admin->getMotdepasse()));
            $admin->setRoles(['ROLE_ADMIN']);

            $admin->setAntecedentsMedicaux(null);
            $admin->setSpecialite(null);
            $admin->setHopital(null);
            $admin->setDisponibilite(null);

            $entityManager->persist($admin);
            $entityManager->flush();

            return $this->redirectToRoute('admin_list');
        }

        return $this->render('admin/create_admin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
   
}