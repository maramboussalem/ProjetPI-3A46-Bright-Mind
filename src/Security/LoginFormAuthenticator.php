<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Repository\UtilisateurRepository; 
use Symfony\Component\Security\Core\Exception\AuthenticationException; 
use App\Repository\ConnectionHistoryRepository;
use App\Entity\ConnectionHistory;
use Doctrine\ORM\EntityManagerInterface;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UtilisateurRepository $utilisateurRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(private UrlGeneratorInterface $urlGenerator, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager)
    {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->entityManager = $entityManager;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');

          // Utiliser la propriété injectée directement
          $utilisateur = $this->utilisateurRepository->findOneByEmail($email);

          if (!$utilisateur) {
    throw new AuthenticationException('Utilisateur non trouvé.');
}

// Enregistrement de l'historique de connexion
$history = new ConnectionHistory();
$history->setUtilisateur($utilisateur);
$history->setTimestamp(new \DateTime());
$history->setEventType('Connexion réussie');
$this->entityManager->persist($history);
$this->entityManager->flush();
        
           if ($utilisateur && !$utilisateur->isActive()) {
             // Remplacer l'exception par un message d'erreur plus précis et éviter le plantage
           throw new \Symfony\Component\Security\Core\Exception\AuthenticationException('Votre compte a été désactivé. Veuillez contacter le support.');
    }
    

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
{
    $roles = $token->getRoleNames(); // Récupérer les rôles de l'utilisateur
    
    if (in_array('ROLE_ADMIN', $roles)) {
        // Rediriger vers le dashboard admin
        return new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
    }

    if (in_array('ROLE_MEDECIN', $roles)) {
        return new RedirectResponse($this->urlGenerator->generate('app_medcin'));
    }
    if (in_array('ROLE_PATIENT', $roles)) {
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }
    
    // Rediriger vers la page d'accueil pour les autres utilisateurs
    return new RedirectResponse($this->urlGenerator->generate('app_home'));
}

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
{
    // Si l'exception est liée à un compte désactivé
    if ($exception->getMessage() === 'Votre compte a été désactivé. Veuillez contacter le support.') {
        // Récupérer l'email de l'utilisateur depuis la requête
        $email = $request->getPayload()->getString('email');

        // Récupérer l'utilisateur à partir de la base de données
        $utilisateur = $this->utilisateurRepository->findOneByEmail($email);

        if ($utilisateur) {
            // Générer l'URL pour la vérification du code de l'utilisateur
            $url = $this->urlGenerator->generate('app_utilisateur_request_activation_code', [
                'id' => $utilisateur->getId()
            ]);

            // Rediriger vers l'URL de vérification
            return new RedirectResponse($url);
        }
    }

    // Redirection par défaut si aucune autre exception
    return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
}


}
