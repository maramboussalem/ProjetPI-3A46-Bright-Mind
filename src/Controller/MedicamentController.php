<?php

namespace App\Controller;

use App\Entity\Medicament;
use App\Form\MedicamentType;
use App\Repository\FournisseurRepository;
use App\Repository\MedicamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\SvgWriter;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/medicament')]
final class MedicamentController extends AbstractController
{

    #[Route('/api/toggle-medicament/{id}', name: 'app_toggle_med', methods: ['POST'])]
    public function toggleUser(Request $request, Medicament $medicament, EntityManagerInterface $entityManager): JsonResponse
    {
       
        if (!$medicament) {
            return new JsonResponse(['success' => false, 'message' => 'medicament not found.'], 404);
        }

        $medicament->setIsshown(!$medicament->isshown());
        $entityManager->persist($medicament);
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => $medicament->isshown() ? 'Medicament est masqué de clients' : 'Medicament est affiché pour les clients.']);
    }


    // Index Route - List all medicaments
    #[Route('/', name: 'app_medicament_index', methods: ['GET'])]
    public function index(
        Request $request, 
        MedicamentRepository $medicamentRepository, 
        FournisseurRepository $fournisseurRepository,
        PaginatorInterface $paginator
    ): Response {
        $fournisseurId = $request->query->get('fournisseur', null);
        $searchTerm = $request->query->get('search', '');

        $queryBuilder = $medicamentRepository->createQueryBuilder('m');

        if ($fournisseurId) {
            $queryBuilder->andWhere('m.fournisseur = :fournisseurId')
                ->setParameter('fournisseurId', $fournisseurId);
        }

        if (!empty($searchTerm)) {
            $queryBuilder->andWhere('m.NomMedicament LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $medicaments = $queryBuilder->getQuery()->getResult();



    
        $pagination2 = $paginator->paginate(
            $medicaments,
            $request->query->getInt('page', 1),
            3  // items per page
        );
    
        $expirationWarnings = [];
        $today = new \DateTime();
        $warningThreshold = (clone $today)->modify('+3 days');
    
        foreach ($medicaments as $medicament) {
            if ($medicament->getExpireat() <= $warningThreshold) {
                $expirationWarnings[$medicament->getId()] = "⚠️ Ce médicament  va expiré bientôt ou déja expiré !";
            }
        }
     
    
        return $this->render('medicament/index.html.twig', [
            'medicaments' => $pagination2,
            'fournisseurs' => $fournisseurRepository->findAll(),
            'selectedFournisseur' => $fournisseurId,
            'searchTerm' => $searchTerm,
            'expirationWarnings' => $expirationWarnings
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



        $data =  $medicament->getNomMedicament() .' | quantité disponible ' . $medicament->getQuantite() .' | Prix : ' . $medicament->getPrix() ;

        $result = Builder::create()
        ->writer(new SvgWriter())
        ->data($data)
         ->encoding(new Encoding('UTF-8'))
         ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
         ->size(300)
        // ->logoPath($logoPath) // Add logo
         //->logoPunchoutBackground(true)
         ->margin(10)
         ->build();
 
     // Generate a Data URI to include image data inline
     $dataUri = $result->getDataUri();





        return $this->render('medicament/show.html.twig', [
            'medicament' => $medicament,
            'qr'=>$dataUri
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



    #[Route('/medicament/statistiques', name: 'app_medicament_statistique', methods: ['GET'])]
    public function statistiques(MedicamentRepository $medicamentRepository): Response
    {
        // Get all medicaments
        $medicaments = $medicamentRepository->findAll();
        $data = [];
    
        // Loop through all medicaments and group by type
        foreach ($medicaments as $medicament) {
            $type = $medicament->getType(); // Make sure getType() is a valid method
            if ($type) {
                // Sum the quantities per type
                if (!isset($data[$type])) {
                    $data[$type] = 0;
                }
                $data[$type] += $medicament->getQuantite();
            }
        }
    
        return $this->render('medicament/statistiques.html.twig', [
            'data' => json_encode($data)
        ]);
    }
    

    















    
}
