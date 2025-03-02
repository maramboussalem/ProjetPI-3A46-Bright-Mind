<?php

namespace App\Security;

use App\Entity\Utilisateur; // Changement de User à Utilisateur
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class GithubAuthenticator extends OAuth2Authenticator
{
    private $clientRegistry;
    private $entityManager;
    private $router;

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager,
        RouterInterface $router
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_github_check';
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $client = $this->clientRegistry->getClient('github');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var GithubResourceOwner $githubUser */
                $githubUser = $client->fetchUserFromToken($accessToken);
                $email = $githubUser->getEmail() ?: $githubUser->getNickname() . '@github.com';

                $user = $this->entityManager->getRepository(Utilisateur::class) // Changement ici
                    ->findOneBy(['email' => $email]);

                if (!$user) {
                    $user = new Utilisateur(); // Changement ici
                    $user->setEmail($email);
                    $user->setNom($githubUser->getNickname()); // Utilisation du username GitHub
                    $user->setPrenom($githubUser->getName() ? explode(' ', $githubUser->getName())[0] ?? 'Inconnu' : 'Inconnu');
                    $user->setAdresse('Adresse');
                    $user->setTelephone('+21655388787');
                    $user->setRole('patient');
                    $user->setRoles(['patient']);
                    $user->setMotdepasse(password_hash(uniqid(), PASSWORD_BCRYPT));
                    $user->setSexe('Non spécifié');
                    $user->setActive(true);
                    $user->setimgUrl('user/img/67c2e93106d90.jpg');
                    
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                }

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->router->generate('app_home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());
        return new RedirectResponse(
            $this->router->generate('app_login', ['error' => $message])
        );
    }
}