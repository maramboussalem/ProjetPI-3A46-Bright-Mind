<?php

namespace App\Controller;

use App\Service\MailchimpService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class MailchimpController extends AbstractController
{
    private MailchimpService $mailchimpService;

    public function __construct(MailchimpService $mailchimpService, ParameterBagInterface $params, Security $security)
    {
        $this->mailchimpService = $mailchimpService;
        $this->params = $params;
        $this->security = $security;
    }

    #[Route('/mailchimp/ping', name: 'mailchimp_ping')]
    public function ping(): Response
    {
        $response = $this->mailchimpService->ping();
        return new Response('<pre>' . print_r($response, true) . '</pre>');
    }

    

    #[Route('/mailchimp/subscribe', name: 'mailchimp_subscribe')]
    public function subscribe(Request $request): Response
    {
        $listId = '0a157eda11';
        $user = $this->security->getUser();
    
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $firstName = $request->request->get('firstName');
            $lastName = $request->request->get('lastName');
            $statusIfNew = $request->request->get('status_if_new', 'subscribed');
    
            $subscriber = [
                'email' => $email,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'status_if_new' => $statusIfNew,
            ];
    
            try {
                $response = $this->mailchimpService->addSubscriber($listId, $subscriber);
                $this->addFlash('success', 'Successfully subscribed to Mailchimp!');
                return $this->redirectToRoute('mailchimp_subscribe');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error: ' . $e->getMessage());
            }
        }
    
        return $this->render('mailchimp/subscribe.html.twig', [
            'form' => null, // No form needed if using raw POST
        ]);
    }
}