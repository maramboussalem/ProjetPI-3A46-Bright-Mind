<?php

namespace App\Controller;

use App\Entity\Medicament;
use App\Form\MedicamentType;
use App\Repository\MedicamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/medicament')]
final class MedicamentController extends AbstractController
{
    // Index Route - List all medicaments
    #[Route('/', name: 'app_medicament_index', methods: ['GET'])]
    public function index(MedicamentRepository $medicamentRepository): Response
    {
        return $this->render('medicament/index.html.twig', [
            'medicaments' => $medicamentRepository->findAll(),
        ]);
    }

    // New Medicament Route - Form to create a new medicament
    #[Route('/new', name: 'app_medicament_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MedicamentRepository $medicamentRepository, ValidatorInterface $validator): Response
    {
        $medicament = new Medicament();
        $form = $this->createForm(MedicamentType::class, $medicament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si un medicament avec le même nom ou autre caractéristique existe déjà
            $existingMedicament = $medicamentRepository->findOneBy([
                'NomMedicament' => $medicament->getNomMedicament(),
            ]);

            if ($existingMedicament) {
                $this->addFlash('error', 'Un medicament avec ce nom existe déjà.');
                return $this->render('medicament/new.html.twig', [
                    'medicament' => $medicament,
                    'form' => $form,
                ]);
            }

            // Symfony validation
            $errors = $validator->validate($medicament);
            if (count($errors) > 0) {
                return $this->render('medicament/new.html.twig', [
                    'medicament' => $medicament,
                    'form' => $form,
                    'errors' => $errors,
                ]);
            }



            $imageFile = $form->get('image')->getData();
           
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '.' . $imageFile->guessExtension();
    
                try {
                    $imageFile->move(
                        $this->getParameter('medicament_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image.');
                    return $this->redirectToRoute('app_student_group_index_administrateur');
                }
    
                $medicament->setImage($newFilename);
            } else {
                $medicament->setImage("default.jpg");

            }
           



            $entityManager->persist($medicament);
            $entityManager->flush();

            return $this->redirectToRoute('app_medicament_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('medicament/new.html.twig', [
            'medicament' => $medicament,
            'form' => $form,
            'errors' => [], // Assurez-vous qu'aucune erreur n'est passée si le formulaire n'est pas soumis
        ]);
    }

    // Show Route - Display a single medicament's details
    #[Route('/{id}', name: 'app_medicament_show', methods: ['GET'])]
    public function show(Medicament $medicament): Response
    {
        return $this->render('medicament/show.html.twig', [
            'medicament' => $medicament,
        ]);
    }

    // Edit Route - Form to edit an existing medicament
    #[Route('/{id}/edit', name: 'app_medicament_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Medicament $medicament, EntityManagerInterface $entityManager, MedicamentRepository $medicamentRepository): Response
    {
        $form = $this->createForm(MedicamentType::class, $medicament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si un autre medicament a le même nom
            $existingMedicament = $medicamentRepository->findOneBy([
                'NomMedicament' => $medicament->getNomMedicament(),
            ]);

            if ($existingMedicament && $existingMedicament->getId() !== $medicament->getId()) {
                $this->addFlash('error', 'Un autre medicament avec ce nom existe déjà.');
                return $this->render('medicament/edit.html.twig', [
                    'medicament' => $medicament,
                    'form' => $form,
                ]);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_medicament_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('medicament/edit.html.twig', [
            'medicament' => $medicament,
            'form' => $form,
        ]);
    }

    // Delete Route - Handle deletion of a medicament
    #[Route('/{id}', name: 'app_medicament_delete', methods: ['POST'])]
    public function delete(Request $request, Medicament $medicament, EntityManagerInterface $entityManager): Response
    {
        // Ensure the CSRF token is valid for the delete action
        if ($this->isCsrfTokenValid('delete' . $medicament->getId(), $request->get('_token'))) {
            $entityManager->remove($medicament);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_medicament_index', [], Response::HTTP_SEE_OTHER);
    }
}
