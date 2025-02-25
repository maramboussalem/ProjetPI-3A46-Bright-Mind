<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Service\TwilioSmsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Password\PasswordHasherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


final class ResetPasswordController extends AbstractController
{
    // Étape 1 : Entrée de l'email
    #[Route('/reset/password/email', name: 'app_reset_password_email', methods: ['GET', 'POST'])]
    public function resetPasswordEmail(Request $request, UtilisateurRepository $utilisateurRepository): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $utilisateur = $utilisateurRepository->findOneBy(['email' => $email]);

            if (!$utilisateur) {
                $this->addFlash('error', 'L\'email que vous avez entré n\'existe pas.');
                return $this->redirectToRoute('app_reset_password_email');
            }

            // Étape 2 : Saisie du numéro de téléphone
            return $this->render('reset_password/phone.html.twig', [
                'utilisateur' => $utilisateur,
            ]);
        }

        return $this->render('reset_password/email.html.twig');
    }

    // Étape 2 : Envoi du code de vérification par SMS
    #[Route('/reset/password/send-code/{id}', name: 'app_reset_password_send_code', methods: ['POST'])]
    public function sendVerificationCode(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager, TwilioSmsService $smsService): Response
    {
        $phone = $request->request->get('phone');
        
        if ($phone !== $utilisateur->getTelephone()) {
            $this->addFlash('error', 'Le numéro de téléphone ne correspond pas à celui enregistré.');
            return $this->redirectToRoute('app_reset_password_email');
        }

        // Générer un code de vérification
        $verificationCode = rand(100000, 999999);
        $utilisateur->setVerificationCode($verificationCode);
        $entityManager->flush();

        // Envoi du code par SMS
        $recipient = $utilisateur->getTelephone();
        $message = "Votre code de réinitialisation de mot de passe est : $verificationCode";

        try {
            $smsService->sendSms($recipient, $message);
            $this->addFlash('success', 'Un code de vérification a été envoyé à votre numéro de téléphone.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'envoi du SMS : ' . $e->getMessage());
        }

        // Étape 3 : Entrée du code de vérification
        return $this->render('reset_password/verify_code.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    // Étape 3 : Vérification du code de vérification
    #[Route('/reset/password/verify/{id}', name: 'app_reset_password_verify', methods: ['POST'])]
    public function verifyCode(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $codeSaisi = $request->request->get('code');
    
        // Vérification si le code saisi correspond au code stocké
        if ($utilisateur->getVerificationCode() !== $codeSaisi) {
            // Si le code est incorrect, affiche un message d'erreur
            $this->addFlash('error', 'Code incorrect. Veuillez réessayer.');
            return $this->redirectToRoute('app_reset_password_email');
        }
    
        // Si le code est correct, on redirige vers la page de mise à jour du mot de passe
        $this->addFlash('success', 'Code vérifié avec succès. Vous pouvez maintenant réinitialiser votre mot de passe.');
        return $this->redirectToRoute('app_reset_password_update', ['id' => $utilisateur->getId()]);
    }

    // Étape 4 : Mise à jour du mot de passe
    #[Route('/reset/password/update/{id}', name: 'app_reset_password_update', methods: ['GET', 'POST'])]
public function updatePassword(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
{
    if ($request->isMethod('POST')) {
        
        $newPassword = $request->request->get('new_password');
        $confirmPassword = $request->request->get('confirm_password');

        // Vérification que les deux champs sont remplis et identiques
        if (empty($newPassword) || empty($confirmPassword)) {
            $this->addFlash('error', 'Veuillez remplir tous les champs.');
            return $this->redirectToRoute('app_reset_password_update', ['id' => $utilisateur->getId()]);
        }

        if ($newPassword !== $confirmPassword) {
            $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
            return $this->redirectToRoute('app_reset_password_update', ['id' => $utilisateur->getId()]);
        }

        // Hachage et mise à jour du mot de passe
        $hashedPassword = $passwordHasher->hashPassword($utilisateur, $newPassword);
        $utilisateur->setPassword($hashedPassword);

        // Suppression du code de vérification pour éviter toute réutilisation
        $utilisateur->setVerificationCode(null);

        $entityManager->flush();

        $this->addFlash('success', 'Votre mot de passe a été mis à jour avec succès.');
        return $this->redirectToRoute('app_login'); // Rediriger vers la page de connexion
    }

    return $this->render('reset_password/update_password.html.twig', [
        'utilisateur' => $utilisateur,
    ]);
}

    }
