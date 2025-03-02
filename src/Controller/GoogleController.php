<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class GoogleController extends AbstractController
{
    #[Route('/login/google', name: 'google_login')]
    public function login(ClientRegistry $clientRegistry)
    {
        return $clientRegistry->getClient('google')->redirect([
            'email', 'profile'
        ], []);
    }

    #[Route('/login/check-google', name: 'google_check')]
    public function checkGoogleLogin(
        Request $request,
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager,
        UtilisateurRepository $utilisateurRepository
    ) {
        $client = $clientRegistry->getClient('google');

        try {
            // Récupérer les informations de l'utilisateur depuis Google
            $googleUser = $client->fetchUser();

            // Chercher l'utilisateur par email
            $email = $googleUser->getEmail();
            $user = $utilisateurRepository->findOneBy(['email' => $email]);

            if (!$user) {
                // Créer un nouvel utilisateur si inexistant
                $user = new Utilisateur();
                $user->setEmail($email);
                $user->setNom($googleUser->getLastName() ?? 'Inconnu');
                $user->setPrenom($googleUser->getFirstName() ?? 'Inconnu');

                $entityManager->persist($user);
                $entityManager->flush();
            }

            // Connecter l'utilisateur manuellement
            $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken(
                $user,
                'main',
                $user->getRoles()
            );
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));

            return $this->redirectToRoute('app_home');

        } catch (IdentityProviderException $e) {
            $this->addFlash('error', 'Erreur lors de la connexion avec Google : ' . $e->getMessage());
            return $this->redirectToRoute('app_login');
        }
    }
}