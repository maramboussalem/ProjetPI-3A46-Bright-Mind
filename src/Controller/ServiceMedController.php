<?php

namespace App\Controller;

use App\Entity\ServiceMed;
use App\Form\ServiceMedType;
use App\Repository\ServiceMedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

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

    #[Route('/Mee', name: 'app_serviceM_med_index', methods: ['GET'])]
    public function indexM(ServiceMedRepository $serviceMedRepository): Response
    {
        return $this->render('service_med/indexM.html.twig', [
            'service_meds' => $serviceMedRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_service_med_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Filesystem $filesystem): Response
    {
        $serviceMed = new ServiceMed();
        $form = $this->createForm(ServiceMedType::class, $serviceMed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageM')->getData(); // Correction du nom du champ

            if ($file) {
                $uploadDirectory = $this->getParameter('service_med_directory');
                $filename = uniqid().'.'.$file->guessExtension();

                try {
                    $file->move($uploadDirectory, $filename);
                    $serviceMed->setImageM($filename);
                } catch (IOExceptionInterface $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }

            $entityManager->persist($serviceMed);
            $entityManager->flush();

            $this->addFlash('success', 'Service médical ajouté avec succès !');

            return $this->redirectToRoute('app_service_med_index');
        }

        return $this->render('service_med/new.html.twig', [
            'service_med' => $serviceMed,
            'form' => $form->createView(),
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
    public function edit(Request $request, ServiceMed $serviceMed, EntityManagerInterface $entityManager, Filesystem $filesystem): Response
    {
        $form = $this->createForm(ServiceMedType::class, $serviceMed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageM')->getData(); // Correction du nom du champ

            if ($file) {
                $uploadDirectory = $this->getParameter('service_med_directory');
                $filename = uniqid().'.'.$file->guessExtension();

                try {
                    if ($serviceMed->getImageM()) {
                        $oldFile = $uploadDirectory . '/' . $serviceMed->getImageM();
                        if ($filesystem->exists($oldFile)) {
                            $filesystem->remove($oldFile);
                        }
                    }

                    $file->move($uploadDirectory, $filename);
                    $serviceMed->setImageM($filename);
                } catch (IOExceptionInterface $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Service médical mis à jour !');

            return $this->redirectToRoute('app_service_med_index');
        }

        return $this->render('service_med/edit.html.twig', [
            'service_med' => $serviceMed,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_service_med_delete', methods: ['POST'])]
    public function delete(Request $request, ServiceMed $serviceMed, EntityManagerInterface $entityManager, Filesystem $filesystem): Response
    {
        if ($this->isCsrfTokenValid('delete'.$serviceMed->getId(), $request->get('_token'))) {
            $uploadDirectory = $this->getParameter('service_med_directory');

            if ($serviceMed->getImageM()) {
                $file = $uploadDirectory . '/' . $serviceMed->getImageM();
                if ($filesystem->exists($file)) {
                    $filesystem->remove($file);
                }
            }

            $entityManager->remove($serviceMed);
            $entityManager->flush();

            $this->addFlash('success', 'Service médical supprimé !');
        }

        return $this->redirectToRoute('app_service_med_index');
    }
}
