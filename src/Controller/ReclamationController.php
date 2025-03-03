<?php
namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Consultation; // Add Consultation entity
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Repository\ConsultationRepository;

#[Route('/reclamation')]
final class ReclamationController extends AbstractController
{
    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
public function index(Request $request, ReclamationRepository $reclamationRepository): Response
{
    // Pagination parameters
    $page = $request->query->getInt('page', 1); // Default to page 1 if no page is provided
    $limit = 10; // Number of records per page
    $offset = ($page - 1) * $limit;

    // Sorting parameters
    $sortBy = $request->query->get('sortBy', 'r.dateCreation'); // Default sort by creation date
    $sortDirection = $request->query->get('sortDirection', 'DESC'); // Default sort direction

    $queryBuilder = $reclamationRepository->createQueryBuilder('r');

    // Apply filters based on user input
    if ($utilisateurId = $request->query->get('utilisateurId')) {
        $queryBuilder->andWhere('r.utilisateurId = :utilisateurId')
            ->setParameter('utilisateurId', $utilisateurId);
    }

    if ($dateCreation = $request->query->get('dateCreation')) {
        $dateCreation = \DateTime::createFromFormat('Y-m-d', $dateCreation);
        if ($dateCreation) {
            $queryBuilder->andWhere('r.dateCreation >= :startDate')
                ->andWhere('r.dateCreation < :endDate')
                ->setParameter('startDate', $dateCreation->format('Y-m-d 00:00:00'))
                ->setParameter('endDate', $dateCreation->format('Y-m-d 23:59:59'));
        }
    }

    if ($dateConsultation = $request->query->get('dateConsultation')) {
        $dateConsultation = \DateTime::createFromFormat('Y-m-d', $dateConsultation);
        if ($dateConsultation) {
            $queryBuilder->leftJoin('r.consultation', 'c')
                ->andWhere('c.dateConsultation >= :startConsultationDate')
                ->andWhere('c.dateConsultation < :endConsultationDate')
                ->setParameter('startConsultationDate', $dateConsultation->format('Y-m-d 00:00:00'))
                ->setParameter('endConsultationDate', $dateConsultation->format('Y-m-d 23:59:59'));
        }
    }

    if ($statut = $request->query->get('statut')) {
        $queryBuilder->andWhere('r.statut = :statut')
            ->setParameter('statut', $statut);
    }

    // Apply sorting
    $queryBuilder->orderBy($sortBy, $sortDirection)
                 ->setFirstResult($offset) // Set the starting point (offset)
                 ->setMaxResults($limit); // Limit the number of results per page

    // Get the paginated results
    $paginator = new Paginator($queryBuilder);
    $totalReclamations = count($paginator); // Total number of records
    $reclamations = $paginator->getQuery()->getResult();

    // Calculate the total number of pages
    $totalPages = ceil($totalReclamations / $limit);

    return $this->render('reclamation/index.html.twig', [
        'reclamations' => $reclamations,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'sortBy' => $sortBy,
        'sortDirection' => $sortDirection,
    ]);
}
    


#[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $request->get('_token'))) {
        $entityManager->remove($reclamation);
        $entityManager->flush();

        $this->addFlash('success', 'Reclamation deleted successfully!');
    } else {
        $this->addFlash('error', 'Invalid CSRF token!');
    }

    return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
}




    
    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $reclamation = new Reclamation();

    // Just create the form without any admin check
    $form = $this->createForm(ReclamationType::class, $reclamation);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($reclamation);
        $entityManager->flush();

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('reclamation/new.html.twig', [
        'reclamation' => $reclamation,
        'form' => $form->createView(),
    ]);
}

#[Route('/reclamation/new2/{consultationId}', name: 'app_reclamation_new2', methods: ['GET', 'POST'])]
public function new2(Request $request, EntityManagerInterface $entityManager, int $consultationId): Response
{
    $consultation = $entityManager->getRepository(Consultation::class)->find($consultationId);

    if (!$consultation) {
        throw $this->createNotFoundException('Consultation not found');
    }

    $reclamation = new Reclamation();
    $reclamation->setStatut('en cours');
    $reclamation->setConsultation($consultation);

    $form = $this->createForm(ReclamationType::class, $reclamation, [
        'is_new2' => true,
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($reclamation);
        $entityManager->flush();

        return $this->redirectToRoute('app_reclamation_index2', [
            'consultationId' => $consultationId // Ensure consultationId is passed
        ]);
    }

    return $this->render('reclamation/new2.html.twig', [
        'reclamation' => $reclamation,
        'form' => $form->createView(),
        'consultation' => $consultation, // Pass consultation to the template
    ]);
}





    

    






    #[Route('/delete2/{id}', name: 'app_reclamation_delete2', methods: ['POST'])]

    public function delete2(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete2' . $reclamation->getId(), $request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();

            $this->addFlash('success', 'Reclamation deleted successfully!');
        } else {
            $this->addFlash('error', 'Invalid CSRF token!');
        }

        return $this->redirectToRoute('app_reclamation_index2', [], Response::HTTP_SEE_OTHER);
    }


// MAP
    #[Route('/map', name: 'app_map')]
    public function showMap(): Response
    {
        // You can pass latitude and longitude dynamically or set it statically
         return $this->render('map/show_map.html.twig', [
        'latitude' => 33.8869,  // Default latitude for Tunisia
        'longitude' => 9.5375,  // Default longitude for Tunisia
        ]);
    }
    

    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET', 'POST'])]
    public function show(Reclamation $reclamation, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = null;

        // If user is not an admin, show the form to respond to the reclamation
        if (!$this->isGranted('ROLE_ADMIN')) {
            $form = $this->createFormBuilder($reclamation)
                ->add('reponse', TextareaType::class, [
                    'label' => 'Your Response',
                    'required' => true,
                ])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();

                return $this->redirectToRoute('app_reclamation_show', ['id' => $reclamation->getId()]);
            }
        }

        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form ? $form->createView() : null, // Only pass form if it exists
        ]);
    }






    #[Route('/{id}/show2', name: 'app_reclamation_show2', methods: ['GET', 'POST'])]
    public function show2(Reclamation $reclamation, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = null;
    
        // If user is not an admin, show the form to respond to the reclamation
        if (!$this->isGranted('ROLE_ADMIN')) {
            $form = $this->createFormBuilder($reclamation)
                ->add('reponse', TextareaType::class, [
                    'label' => 'Your Response',
                    'required' => true,
                ])
                ->getForm();
    
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();
    
                return $this->redirectToRoute('app_reclamation_show2', ['id' => $reclamation->getId()]);
            }
        }
    
        return $this->render('reclamation/show2.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form ? $form->createView() : null, // Only pass form if it exists
        ]);
    }
    











    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        // Pass 'is_admin' to the form based on the user role
        $form = $this->createForm(ReclamationType::class, $reclamation, [
            'is_admin' => $this->isGranted('ROLE_ADMIN')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/respond', name: 'app_reclamation_respond', methods: ['GET', 'POST'])]
    public function respond(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        // Create form for admin to respond
        $form = $this->createFormBuilder($reclamation)
            ->add('reponse', TextareaType::class, [
                'label' => 'Admin Response',
                'required' => true,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_show', ['id' => $reclamation->getId()]);
        }

        return $this->render('reclamation/respond.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }


     #[Route('/check-consultation/{date}', name: 'check_consultation')]
    public function checkConsultation($date, ConsultationRepository $consultationRepository): JsonResponse
    {
        // Convert the string to a DateTime object
        $dateConsultation = \DateTime::createFromFormat('Y-m-d', $date);

        // Check if there's a consultation for that date
        $consultation = $consultationRepository->findOneBy(['dateConsultation' => $dateConsultation]);

        // Return a JSON response
        return new JsonResponse(['exists' => $consultation !== null]);
    }





    public function checkConsultationExists(Request $request, ConsultationRepository $consultationRepository)
    {
        // Get the date from the request
        $date = $request->query->get('date');
        $date = \DateTime::createFromFormat('Y-m-d', $date); // Convert string to DateTime object
    
        // Check if a consultation exists for this date
        $consultation = $consultationRepository->findOneBy(['dateConsultation' => $date]);
    
        // Return a JSON response
        return new JsonResponse([
            'exists' => $consultation !== null,
        ]);





    }








    #[Route('/reclamation/index2', name: 'app_reclamation_index2', methods: ['GET'])]
    public function index2(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/index2.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }




    // -----------------rout show me the reclamation of this user onlyyyyyyyy------------------------
  
    // #[Route('/reclamation/index2', name: 'app_reclamation_index2', methods: ['GET'])]
    // public function index2(ReclamationRepository $reclamationRepository): Response
    // {
    //     // Get the currently logged-in user
    //     $user = $this->getUser();
    
    //     // Fetch reclamations for the logged-in user
    //     $reclamations = $reclamationRepository->findBy(['utilisateurId' => $user->getId()]);
    
    //     return $this->render('reclamation/index2.html.twig', [
    //         'reclamations' => $reclamations,
    //     ]);












    // STATISTIC 


    #[Route('/reclamation/statistics', name: 'app_reclamation_statistics')]
    public function statistics(ReclamationRepository $reclamationRepository): Response
    {
        $reclamations = $reclamationRepository->findAll();
    
        $subjectCounts = [
            'Problème avec la consultation en ligne' => 0,
            'Problème de communication avec le médecin' => 0,
            'Erreur dans le diagnostic' => 0,
            'Médicament non adapté' => 0,
            'Autres' => 0,
        ];
    
        foreach ($reclamations as $reclamation) {
            $subject = $reclamation->getSujet();
            if (isset($subjectCounts[$subject])) {
                $subjectCounts[$subject]++;
            }
        }
    
        return $this->render('reclamation/statistics.html.twig', [
            'subjectCounts' => $subjectCounts,
        ]);


}




// MAP







}