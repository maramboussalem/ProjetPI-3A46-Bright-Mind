<?php
namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UtilisateurRepository;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/index_back.html.twig');
    }

    #[Route('/create-admin', name: 'create_admin')]
    public function createAdmin(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $admin = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $admin->setMotdepasse($passwordHasher->hashPassword($admin, $admin->getMotdepasse()));
            $admin->setRoles(['admin']);
            $admin->setRole('admin');
            $admin->setAntecedentsMedicaux(null);
            $admin->setSpecialite(null);
            $admin->setHopital(null);
            $admin->setDisponibilite(null);

            $entityManager->persist($admin);
            $entityManager->flush();

            return $this->redirectToRoute('admin_list');
        }

        return $this->render('admin/create_admin.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/list', name: 'admin_list', methods: ['GET'])]
    public function listAdmins(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('admin/list_admins.html.twig', [
           'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'admin_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateur $admin, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $admin->getId(), $request->get('_token'))) {
            $entityManager->remove($admin);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_dashboard', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/admin/edit/{id}', name: 'admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateur $admin, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UtilisateurType::class, $admin);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $admin->setRoles(['admin']);
            $admin->setRole('admin');
    
            $admin->setAntecedentsMedicaux(null);
            $admin->setSpecialite(null);
            $admin->setHopital(null);
            $admin->setDisponibilite(null);
    
            if ($form->get('motdepasse')->getData()) {
                $admin->setMotdepasse($passwordHasher->hashPassword($admin, $form->get('motdepasse')->getData()));
            }
    
            $entityManager->flush();
            return $this->redirectToRoute('admin_list');
        }
    
        return $this->render('admin/edit_admin.html.twig', [
            'form' => $form->createView(),
            'admin' => $admin,
        ]);
    }
}