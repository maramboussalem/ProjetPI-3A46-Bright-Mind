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
                $this->addFlash('success', 'Successfully subscribed !');
                
            } catch (\Exception $e) {
                $this->addFlash('error', 'Already Subcribed');
            }
            $referer = $request->headers->get('referer');
            return $this->redirect($referer ?: $this->generateUrl('compagne_public'));
        }
    
        return $this->redirectToRoute('compagne_public');
    }
}