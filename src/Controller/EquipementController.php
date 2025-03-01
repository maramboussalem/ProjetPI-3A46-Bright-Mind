<?php
namespace App\Controller;

use App\Entity\Equipement;
use App\Form\EquipementType;
use App\Repository\EquipementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCodeBundle\Response\QrCodeResponse;

#[Route('/equipement')]
final class EquipementController extends AbstractController
{
    #[Route('/', name: 'app_equipement_index', methods: ['GET'])]
    public function index(EquipementRepository $equipementRepository): Response
    {
        return $this->render('equipement/index.html.twig', [
            'equipements' => $equipementRepository->findAll(),
        ]);
    }

    #[Route('/M', name: 'app_equipementM_index', methods: ['GET'])]
    public function indexM(EquipementRepository $equipementRepository): Response
    {
        return $this->render('equipement/indexM.html.twig', [
            'equipements' => $equipementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_equipement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipement = new Equipement();
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('img')->getData();

            if ($file) {
                $uploadDirectory = $this->getParameter('equipement_directory');
                $filename = uniqid() . '.' . $file->guessExtension();

                try {
                    $file->move($uploadDirectory, $filename);
                    $equipement->setImg($filename);
                } catch (IOExceptionInterface $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $entityManager->persist($equipement);
            $entityManager->flush();

            return $this->redirectToRoute('app_equipement_index');
        }

        return $this->render('equipement/new.html.twig', [
            'equipement' => $equipement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_equipement_show', methods: ['GET'])]
    public function show(Equipement $equipement): Response
    {
        return $this->render('equipement/show.html.twig', [
            'equipement' => $equipement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_equipement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipement $equipement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);
        $filesystem = new Filesystem();

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('img')->getData();

            if ($file) {
                $uploadDirectory = $this->getParameter('equipement_directory');
                $filename = uniqid() . '.' . $file->guessExtension();

                try {
                    // Supprimer l'ancienne image si elle existe
                    if ($equipement->getImg()) {
                        $oldFilePath = $uploadDirectory . '/' . $equipement->getImg();
                        if ($filesystem->exists($oldFilePath)) {
                            $filesystem->remove($oldFilePath);
                        }
                    }

                    $file->move($uploadDirectory, $filename);
                    $equipement->setImg($filename);
                } catch (IOExceptionInterface $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_equipement_index');
        }

        return $this->render('equipement/edit.html.twig', [
            'equipement' => $equipement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_equipement_delete', methods: ['POST'])]
    public function delete(Request $request, Equipement $equipement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $equipement->getId(), $request->get('_token'))) {
            $filesystem = new Filesystem();
            $uploadDirectory = $this->getParameter('equipement_directory');

            // Supprimer l'image associée
            if ($equipement->getImg()) {
                $filePath = $uploadDirectory . '/' . $equipement->getImg();
                if ($filesystem->exists($filePath)) {
                    $filesystem->remove($filePath);
                }
            }

            $entityManager->remove($equipement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_equipement_index');
    }

    #[Route('/{id}/qr-code', name: 'equipement_qr_code')]
    public function generateQrCode(Equipement $equipement, BuilderInterface $customQrCodeBuilder): Response
    {
        // Generate unique content for the QR code (including quantity and state)
        $qrContent = json_encode([
            'NomEquipement' => $equipement->getNomEquipement(),
            'QuantiteStock' => $equipement->getQuantiteStock(),
            'EtatEquipement' => $equipement->getEtatEquipement(),
        ]);
        
         // Generate the QR code
         $result = $customQrCodeBuilder->build([
            'data' => $qrContent,
            'size' => 300,
            'margin' => 10,
        ]);
        

    // Return QR code as an image response
    return new QrCodeResponse($result);
}
}
