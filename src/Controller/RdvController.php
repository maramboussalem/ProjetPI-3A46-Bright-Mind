<?php

namespace App\Controller;

use App\Entity\Rdv;
use App\Entity\ServiceMed;
use App\Form\RdvType;
use App\Repository\RdvRepository; 
use App\Repository\ServiceMedRepository;
use App\Repository\DisponibiliteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rdv')]
class RdvController extends AbstractController
{
    // Route to display all RDVs
    #[Route('/', name: 'app_rdv_index', methods: ['GET'])]
    public function index(RdvRepository $rdvRepository): Response
    {
        // Fetch RDVs with their related serviceName (ServiceMed) using LEFT JOIN
        $rdvs = $rdvRepository->createQueryBuilder('r')
            ->leftJoin('r.serviceName', 's')  // Left join to load serviceName
            ->addSelect('s')  // Select serviceName to ensure it's loaded
            ->getQuery()
            ->getResult();
    
        return $this->render('rdv/index.html.twig', [
            'rdvs' => $rdvs,  // Pass the fetched RDVs to the template
        ]);
    }

    // Route to display the RDV creation form
    #[Route('/new', name: 'app_rdv_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rdv = new Rdv();
        $form = $this->createForm(RdvType::class, $rdv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rdv);
            $entityManager->flush();

            $this->addFlash('success', 'Rendez-vous créé avec succès!');
            return $this->redirectToRoute('app_rdv_index');
        }

        return $this->render('rdv/new.html.twig', [
            'rdv' => $rdv,
            'form' => $form->createView(),
        ]);
    }

    // Route to create RDV from service, passing only serviceMedId
    #[Route('/new/from/service/{serviceMedId}', name: 'app_rdv_new_from_service', methods: ['GET', 'POST'])]
    public function newFromService(
        $serviceMedId,
        ServiceMedRepository $serviceMedRepository,
        DisponibiliteRepository $disponibiliteRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $serviceMed = $serviceMedRepository->find($serviceMedId);

        if (!$serviceMed) {
            throw $this->createNotFoundException('Service médical non trouvé');
        }

        $rdv = new Rdv();
        $rdv->setServiceName($serviceMed);  // Correctly assign service

        $disponibilites = $disponibiliteRepository->findAll();
        $form = $this->createForm(RdvType::class, $rdv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rdv);
            $entityManager->flush();

            $this->addFlash('success', 'Rendez-vous créé avec succès!');
            return $this->redirectToRoute('app_rdv_index');
        }

        return $this->render('rdv/new_from_service.html.twig', [
            'rdv' => $rdv,
            'form' => $form->createView(),
            'service_med' => $serviceMed,  // Pass serviceMed to twig to use in form
            'disponibilites' => $disponibilites,
        ]);
    }




 // Route to delete an RDV
 #[Route('/{id}/delete', name: 'app_rdv_delete', methods: ['POST'])]
 public function delete(Rdv $rdv, EntityManagerInterface $entityManager): Response
 {
     // Remove the RDV from the database
     $entityManager->remove($rdv);
     $entityManager->flush();

     // Add a flash message to inform the user of the deletion
     $this->addFlash('success', 'Rendez-vous supprimé avec succès!');

     // Redirect to the RDV index page
     return $this->redirectToRoute('app_rdv_index');
 }





    
}
