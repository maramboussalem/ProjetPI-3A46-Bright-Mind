<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $utilisateur = $this->getUser(); 
        return $this->render('profile/index.html.twig', [
            'utilisateur' => $utilisateur,
            'prenom' => $utilisateur->getPrenom(),
            'nom' => $utilisateur->getNom(),
        ]);
    }


    #[Route('/profileAdmin', name: 'app_profile_admin')]
    public function index_admin(): Response
    {
        $utilisateur = $this->getUser();
        return $this->render('profile/index_admin.html.twig', [
            'utilisateur' => $utilisateur,
            'prenom' => $utilisateur->getPrenom(),
            'nom' => $utilisateur->getNom(),
        ]);
    }
}
