<?php

namespace App\Controller;

use App\Entity\ServiceMed;
use App\Form\ServiceMedType;
use App\Repository\ServiceMedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/service/med')]
final class ServiceMedController extends AbstractController
{
    #[Route(name: 'app_service_med_index', methods: ['GET'])]
    public function index(ServiceMedRepository $serviceMedRepository): Response
    {
        return $this->render('service_med/index.html.twig', [
            'service_meds' => $serviceMedRepository->findAll(),
        ]);

        
    }
    

    #[Route('/new', name: 'app_service_med_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $serviceMed = new ServiceMed();
        $form = $this->createForm(ServiceMedType::class, $serviceMed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($serviceMed);
            $entityManager->flush();

            return $this->redirectToRoute('app_service_med_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('service_med/new.html.twig', [
            'service_med' => $serviceMed,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_service_med_show', methods: ['GET'])]
    public function show(ServiceMed $serviceMed): Response
    {
        return $this->render('service_med/show.html.twig', [
            'service_med' => $serviceMed,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_service_med_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ServiceMed $serviceMed, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ServiceMedType::class, $serviceMed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_service_med_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('service_med/edit.html.twig', [
            'service_med' => $serviceMed,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_service_med_delete', methods: ['POST'])]
    public function delete(Request $request, ServiceMed $serviceMed, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$serviceMed->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($serviceMed);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_service_med_index', [], Response::HTTP_SEE_OTHER);
    }
}
