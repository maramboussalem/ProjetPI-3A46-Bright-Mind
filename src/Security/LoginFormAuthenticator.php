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
use App\Entity\ConnectionHistory;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\EmailService;


class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UtilisateurRepository $utilisateurRepository;
    private EntityManagerInterface $entityManager;
    private EmailService $emailService;

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        UtilisateurRepository $utilisateurRepository,
        EntityManagerInterface $entityManager,
        EmailService $emailService
        
    ) {

        $this->utilisateurRepository = $utilisateurRepository;
        $this->entityManager = $entityManager;
        $this->emailService = $emailService;
        
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');
        $password = $request->getPayload()->getString('password');
        $session = $request->getSession();

        // Vérification des champs vides
        if (empty($email) || empty($password)) {
            throw new AuthenticationException('Veuillez remplir tous les champs.');
        }

        // Vérifier si l'utilisateur est bloqué
        $lockoutTime = $session->get('lockout_time');
        if ($lockoutTime && $lockoutTime > time()) {
            throw new AuthenticationException('Trop de tentatives échouées. Veuillez attendre.');
        }

        // Vérification de l'utilisateur
        $utilisateur = $this->utilisateurRepository->findOneByEmail($email);
        if (!$utilisateur) {
            throw new AuthenticationException('Email ou mot de passe incorrect.');
        }

        // Vérification si le compte est actif
        if (!$utilisateur->isActive()) {
            throw new AuthenticationException('Votre compte a été désactivé. Demandez un code d’activation pour le réactiver.');
        }

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Réinitialiser les tentatives échouées et le temps de blocage
        $session = $request->getSession();
        $session->remove('login_attempts');
        $session->remove('lockout_time');

        // Enregistrement de l'historique de connexion
        $utilisateur = $token->getUser();
        $history = new ConnectionHistory();
        $history->setUtilisateur($utilisateur);
        $history->setTimestamp(new \DateTime());
        $history->setEventType('Connexion réussie');
        $this->entityManager->persist($history);
        $this->entityManager->flush();

        $roles = $token->getRoleNames();
        if (in_array('ROLE_ADMIN', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
        }
        if (in_array('ROLE_MEDECIN', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('app_medcin'));
        }
        if (in_array('ROLE_PATIENT', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('app_home'));
        }
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $session = $request->getSession();
        $email = $request->getPayload()->getString('email');

        if ($exception->getMessage() === 'Votre compte a été désactivé. Demandez un code d’activation pour le réactiver.') {
            $utilisateur = $this->utilisateurRepository->findOneByEmail($email);
            if ($utilisateur) {
                // Générer un nouveau token d'activation
                $activationToken = bin2hex(random_bytes(16));
                $utilisateur->setActivationToken($activationToken);
                $this->entityManager->flush();
    
                $activationLink = $this->urlGenerator->generate('app_activate', ['token' => $activationToken], UrlGeneratorInterface::ABSOLUTE_URL);

$htmlContent = "
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 20px;
                text-align: center;
            }
            .container {
                background: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
                max-width: 600px;
                margin: auto;
            }
            .button {
                background-color:rgb(73, 157, 247);
                color: white;
                padding: 10px 20px;
                text-decoration: none;
                border-radius: 5px;
                display: inline-block;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>Réactivation de votre compte</h2>
            <p>Madame, Monsieur,</p>
            <p>Nous vous informons que votre compte est actuellement désactivé. Si vous souhaitez le réactiver, nous vous invitons à cliquer sur le lien ci-dessous :</p>
            <a href='$activationLink' class='button'>Réactiver mon compte</a>
            <p>Si vous n'êtes pas à l'origine de cette demande, veuillez ignorer cet e-mail.</p>
            <p>Restant à votre disposition pour toute information complémentaire, nous vous prions d’agréer, Madame, Monsieur, l’expression de nos salutations distinguées.</p>
            <p>Cordialement,<br>L'équipe [Bright-Mind]</p>
        </div>
    </body>
    </html>
";

$this->emailService->sendEmail(
    $utilisateur->getEmail(),
    'Réactivation de votre compte',
    $htmlContent,
    true // Ce paramètre indique que c'est un email HTML
);
                // Rediriger vers une page informant l'utilisateur de l'envoi de l'email
                return new RedirectResponse($this->urlGenerator->generate('app_activation_email_sent'));
            }
        }
        // Gérer les tentatives échouées pour un mot de passe incorrect
        if ($exception->getMessage() === 'Email ou mot de passe incorrect.') {
            $attempts = $session->get('login_attempts', 0) + 1;
            $session->set('login_attempts', $attempts);

            if ($attempts >= 3) {
                // Bloquer pendant 30 secondes
                $lockoutTime = time() + 30;
                $session->set('lockout_time', $lockoutTime);
                $session->set('login_attempts', 0); // Réinitialiser les tentatives
                $exception = new AuthenticationException('Trop de tentatives échouées. Veuillez attendre.');
            }
        }

        // Stocker l'erreur dans la session avec le temps de blocage si applicable
        $session->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
        if ($session->get('lockout_time')) {
            $session->set('lockout_end', $session->get('lockout_time')); // Stocker le temps de fin pour le compte à rebours
        }

        return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}