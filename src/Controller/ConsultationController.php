<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Entity\Utilisateur; 
use App\Form\ConsultationType;
use App\Repository\ConsultationRepository;
use App\Repository\UtilisateurRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;


#[Route('/consultation')]
final class ConsultationController extends AbstractController
{
    #[Route('/', name: 'app_consultation_index', methods: ['GET'])]
    public function index(ConsultationRepository $consultationRepository ): Response
    {
        return $this->render('consultation/index.html.twig', [  // Standard consultations list
            'consultations' => $consultationRepository->findAll(),
        ]);
    }

    #[Route('/index2', name: 'app_consultation_index2', methods: ['GET'])]
    public function index2(ConsultationRepository $consultationRepository, Security $security): Response
    {
        $user = $security->getUser();
        return $this->render('consultation/index2.html.twig', [
            'consultations' => $consultationRepository->findBy(['user' => $user]),
        ]);
    }
    
    #[Route('/new', name: 'app_consultation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $consultation = new Consultation();
        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($consultation);
            $entityManager->flush();

            return $this->redirectToRoute('app_consultation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('consultation/new.html.twig', [
            'consultation' => $consultation,
            'form' => $form,
        ]);
    }
    






    
    #[Route('/{id}', name: 'app_consultation_show', methods: ['GET'])]
    public function show(Consultation $consultation): Response
    {
        return $this->render('consultation/show.html.twig', [
            'consultation' => $consultation,
        ]);
    }




    #[Route('/{id}/details', name: 'app_consultation_show2', methods: ['GET'])]
public function show2(Consultation $consultation): Response
{
    return $this->render('consultation/show2.html.twig', [
        'consultation' => $consultation,
    ]);
}






    #[Route('/{id}/edit', name: 'app_consultation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Consultation $consultation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Redirect to the appropriate consultations list page after editing
            return $this->redirectToRoute('app_consultation_index', [], Response::HTTP_SEE_OTHER);  // Redirect to index or index2 based on your choice
        }

        return $this->render('consultation/edit.html.twig', [
            'consultation' => $consultation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_consultation_delete', methods: ['POST'])]
    public function delete(Request $request, Consultation $consultation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $consultation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($consultation);
            $entityManager->flush();
        }

        // Redirect to the appropriate consultations list page after deletion
        return $this->redirectToRoute('app_consultation_index', [], Response::HTTP_SEE_OTHER);  // Redirect to index or index2 based on your choice
    }
}
