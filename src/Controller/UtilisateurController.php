<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\ConnectionHistory;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\ByteString;
use Twilio\Rest\Client;
use App\Service\TwilioSmsService;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\FormError;
use App\Repository\ConnectionHistoryRepository; // Ajoutez ce use pour l'historique des connexions
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;


#[Route('/utilisateur')]
final class UtilisateurController extends AbstractController
{
    #[Route(name: 'app_utilisateur_index', methods: ['GET'])]
    public function index(Request $request, UtilisateurRepository $utilisateurRepository)
    {
        $searchTerm = $request->query->get('search');
        if ($searchTerm) {
            $utilisateurs = $utilisateurRepository->searchByTerm($searchTerm);
        } else {
            $utilisateurs = $utilisateurRepository->findAll(); 
        }

        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }

    #[Route('/new', name: 'app_utilisateur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, TwilioSmsService $smsService, SessionInterface $session): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
        $captchaSaisi = $form->get('captcha')->getData();
        $captchaSession = $session->get('captcha_text');

        if ($captchaSaisi !== $captchaSession) {
            $form->get('captcha')->addError(new \Symfony\Component\Form\FormError('Le code CAPTCHA est incorrect.'));
            return $this->render('utilisateur/new.html.twig', [
                'form' => $form->createView(),
            ]);
        }
            $hashedPassword = $passwordHasher->hashPassword($utilisateur, $utilisateur->getMotdepasse());
            $utilisateur->setPassword($hashedPassword);
    
            $role = $form->get('role')->getData();
            $utilisateur->setRoles([$role]);
    
            if ($role === 'patient') {
                $utilisateur->setAntecedentsMedicaux($form->get('antecedentsMedicaux')->getData());
            } elseif ($role === 'medecin') {
                $utilisateur->setSpecialite($form->get('specialite')->getData());
                $utilisateur->setHopital($form->get('hopital')->getData());
            }
        /** @var UploadedFile $file */
        $file = $form->get('file')->getData();

        if ($file) {
            $fileName = uniqid() . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('user_directory'), 
                $fileName
            );
            $utilisateur->setimgUrl('user/img/' . $fileName);
        }
            $entityManager->persist($utilisateur);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_login');
        }
        return $this->render('utilisateur/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/utilisateur/{id}', name: 'app_utilisateur_show')]
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }
    
    #[Route('/{id}/edit', name: 'app_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, SessionInterface $session): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

        $captchaSaisi = $form->get('captcha')->getData(); 
        $captchaSession = $session->get('captcha_text'); 

        if ($captchaSaisi !== $captchaSession) {
            $form->get('captcha')->addError(new \Symfony\Component\Form\FormError('Le code CAPTCHA est incorrect.'));
            return $this->render('utilisateur/edit.html.twig', [
                'utilisateur' => $utilisateur,
                'form' => $form->createView(),
            ]);
        }
            $newPassword = $form->get('motdepasse')->getData();
            if (!empty($newPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($utilisateur, $newPassword);
                $utilisateur->setPassword($hashedPassword);

                $history = new ConnectionHistory();
                $history->setUtilisateur($utilisateur);
                $history->setTimestamp(new \DateTime());
                $history->setEventType('Changement de mot de passe');
                $entityManager->persist($history);
            }

            $history = new ConnectionHistory();
            $history->setUtilisateur($utilisateur);
            $history->setTimestamp(new \DateTime());
            $history->setEventType('Modification des informations');
            $entityManager->persist($history);

            $role = $form->get('role')->getData();
            $utilisateur->setRoles([$role]);

            if ($role === 'patient') {
                $utilisateur->setAntecedentsMedicaux($form->get('antecedentsMedicaux')->getData());

            } elseif ($role === 'medecin') {
                $utilisateur->setSpecialite($form->get('specialite')->getData());
                $utilisateur->setHopital($form->get('hopital')->getData());
            }
    
            /** @var UploadedFile $file */
        $file = $form->get('file')->getData();

        if ($file) {
            $fileName = uniqid() . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('user_directory'), 
                $fileName
            );
            $utilisateur->setimgUrl('user/img/' . $fileName);
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

    #[Route('/{id}/activate', name: 'app_utilisateur_activate', methods: ['POST'])]
    public function activate(Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        if (!$utilisateur->isActive()) {
            $utilisateur->setActive(true);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_utilisateur_index');
    }

    #[Route('/{id}/deactivate', name: 'app_utilisateur_deactivate', methods: ['POST'])]
    public function deactivate(Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        if ($utilisateur->isActive()) {
            $utilisateur->setActive(false);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_utilisateur_index');
    }

 #[Route('/{id}/verify-code', name: 'app_utilisateur_verify_code', methods: ['GET', 'POST'])]
public function verifyCode(Request $request, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager, int $id): Response
{
    $utilisateur = $utilisateurRepository->find($id);

    if (!$utilisateur) {
        throw $this->createNotFoundException('Utilisateur non trouvé.');
    }

    // Si l'utilisateur est déjà activé, rediriger vers la page d'accueil
    if ($utilisateur->isActive()) {
        $this->addFlash('info', 'Votre compte est déjà activé.');
        return $this->redirectToRoute('app_utilisateur_index');
    }

    // Si le formulaire est soumis
    if ($request->isMethod('POST')) {
        $codeSaisi = $request->request->get('code');

        // Vérifie si le code saisi est correct
        if ($utilisateur->getVerificationCode() === $codeSaisi) {
            // Code valide, activer le compte
            $utilisateur->setActive(true);
            $entityManager->flush();
            $this->addFlash('success', 'Votre compte a été activé avec succès !');
            return $this->redirectToRoute('app_login');
        } else {
            $this->addFlash('error', 'Code incorrect. Veuillez réessayer.');
        }
    }

    return $this->render('utilisateur/verify_code.html.twig', [
        'utilisateur' => $utilisateur,
    ]);
}


 #[Route('/{id}/request-activation-code', name: 'app_utilisateur_request_activation_code', methods: ['GET', 'POST'])]
public function requestActivationCode(Utilisateur $utilisateur, Request $request, EntityManagerInterface $entityManager, TwilioSmsService $smsService): Response
{
    // Vérifie si l'utilisateur est désactivé
    if ($utilisateur->isActive()) {
        $this->addFlash('info', 'Votre compte est déjà activé.');
        return $this->redirectToRoute('app_utilisateur_index');
    }

    // Générer un code de vérification unique
    $verificationCode = rand(100000, 999999); // Génère un code aléatoire à 6 chiffres
    $utilisateur->setVerificationCode($verificationCode);
    $entityManager->flush();

    // Envoi du code de vérification par SMS
    $recipient = $utilisateur->getTelephone();
    $message = "Votre code de vérification est : $verificationCode";

    try {
        $smsService->sendSms($recipient, $message);
        $this->addFlash('success', 'Un code de vérification a été envoyé à votre numéro de téléphone.');
    } catch (\Exception $e) {
        $this->addFlash('error', 'Erreur lors de l\'envoi du SMS : ' . $e->getMessage());
    }

    // Affichage de la page account_inactive.html.twig après la demande du code
    return $this->render('utilisateur/account_inactive.html.twig', [
        'utilisateur' => $utilisateur,
    ]);
}

#[Route('/search', name: 'app_utilisateur_search', methods: ['GET', 'POST'])]
public function search(Request $request, UtilisateurRepository $utilisateurRepository): Response
{
    $term = $request->query->get('q', '');  // Ensure the "q" parameter is being captured correctly

    // Perform the search if a term is provided, otherwise return all users
    $utilisateurs = !empty($term) ? $utilisateurRepository->searchByName($term) : $utilisateurRepository->findAll();

    // Return the view with the users and search term
    return $this->render('utilisateur/index.html.twig', [
        'utilisateurs' => $utilisateurs,
        'term' => $term,  // Pass the search term to the template
    ]);
}

#[Route('/statistiques', name: 'app_utilisateur_statistiques')]
    public function statistiques(UtilisateurRepository $utilisateurRepository): Response
    {
        $totalUtilisateurs = $utilisateurRepository->count([]);
        $totalMedecins = $utilisateurRepository->count(['role' => 'medecin']);
        $totalPatients = $utilisateurRepository->count(['role' => 'patient']);

        return $this->render('utilisateur/statistiques.html.twig', [
            'total_utilisateurs' => $totalUtilisateurs,
            'total_medecins' => $totalMedecins,
            'total_patients' => $totalPatients,
        ]);

    }

    #[Route('/{id}/generate-user-qr', name: 'app_utilisateur_generate_user_qr', methods: ['GET'])]
public function generateUserQRCode(Utilisateur $utilisateur, ConnectionHistoryRepository $historyRepository): Response
{
    $userInfoText = "Nom: " . $utilisateur->getNom() . "\n";
    $userInfoText .= "Prenom: " . $utilisateur->getPrenom() . "\n"; 
    $userInfoText .= "Email: " . $utilisateur->getEmail() . "\n";
    $userInfoText .= "Adresse: " . $utilisateur->getAdresse() . "\n";
    $userInfoText .= "Telephone: " . $utilisateur->getTelephone() . "\n";
    $userInfoText .= "Role: " . $utilisateur->getRole() . "\n";

    if ($utilisateur->getRole() === 'patient') {
        $userInfoText .= "Antécédents médicaux: " . $utilisateur->getAntecedentsMedicaux() . "\n";
    } elseif ($utilisateur->getRole() === 'medecin') {
        $userInfoText .= "Spécialité: " . $utilisateur->getSpecialite() . "\n";
        $userInfoText .= "Hôpital: " . $utilisateur->getHopital() . "\n";
    }
    $historique = $historyRepository->findBy(
        ['utilisateur' => $utilisateur],
        ['timestamp' => 'DESC']
    );

    $userInfoText .= "\nHistorique des connexions:\n";
    foreach ($historique as $event) {
        $userInfoText .= $event->getTimestamp()->format('d/m/Y H:i:s') . " - " . $event->getEventType() . "\n";
    }
    $qrCode = new QrCode($userInfoText);
    $qrCode->setSize(300); 
    $qrCode->setMargin(10); 

    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    
    return new Response(
        $result->getString(),
        Response::HTTP_OK,
        ['Content-Type' => 'image/png']
    );
}

   #[Route('/{id}/historique', name: 'app_utilisateur_historique')]
   public function historique(Utilisateur $utilisateur, ConnectionHistoryRepository $historyRepository): Response
   {
     $historique = $historyRepository->findBy(
        ['utilisateur' => $utilisateur],
        ['timestamp' => 'DESC']
     );

     return $this->render('utilisateur/historique.html.twig', [
        'historique' => $historique,
     ]);
    }
}
