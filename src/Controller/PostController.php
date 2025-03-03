<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Subscriber;
use App\Service\EmailService;



#[Route('/post')]
final class PostController extends AbstractController
{
    #[Route(name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

 
    
#[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, EmailService $emailService): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
    
            if ($file) {
                $fileName = uniqid() . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('upload_directory'),
                    $fileName
                );
                $post->setImageUrl('front/compagne/img/' . $fileName);
            }
    
            // Set published date and save post
            $post->setPublishedAt(new \DateTime());
            $entityManager->persist($post);
            $entityManager->flush();
    
            // Fetch all users
           // $users = $entityManager->getRepository('App\Entity\Utilisateur')->findAll();
    
            // Send email to each user with post details
            /*foreach ($users as $user) {
                $emailSubject = 'New Post: ' . $post->getTitle();
                $emailBody = sprintf(
                    "Hello %s %s,\n\nA new post has been published!\n\nTitle: %s\nContent: %s\nImage: %s\n\nCheck it out on our site!\n\nBest regards,\nYour App Team",
                    $user->getPrenom() ?? 'User', // Assuming prenom and nom exist; adjust if different
                    $user->getNom() ?? '',
                    $post->getTitle(),
                    $post->getContent(),
                    $post->getImageUrl()?? 'No image uploaded'
                );
    
                $emailService->sendEmail(
                    $user->getEmail(),
                    $emailSubject,
                    $emailBody
                );
            }
    */
            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{id}', name: 'app_post_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(string $id, PostRepository $postRepository, EntityManagerInterface $entityManager): Response
    {
        $id = (int) $id; // Cast to integer
        $post = $postRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('No post found for ID ' . $id);
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/statistics', name: 'app_post_statistics', methods: ['GET'])]
    public function statistics(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();
        $totalViews = array_sum(array_map(fn($post) => $post->getViews() ?? 0, $posts));
        $totalComments = array_sum(array_map(fn($post) => $post->getCommentCount() ?? 0, $posts));

        return $this->render('post/statistics.html.twig', [
            'total_views' => $totalViews,
            'total_comments' => $totalComments,
            'posts' => $posts,
        ]);
    }
}
