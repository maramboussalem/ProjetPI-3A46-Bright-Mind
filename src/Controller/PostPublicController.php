<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface; 
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

final class PostPublicController extends AbstractController
{

#[Route('/compagne', name: 'compagne_public')]
public function index(EntityManagerInterface $entityManager): Response
{
    $posts = $entityManager->getRepository(Post::class)->findAll();

    return $this->render('home/compagne.html.twig', [
        'posts' => $posts
    ]);
}
#[Route('/compagne/{id}', name: 'post_detail')]
public function show(int $id, EntityManagerInterface $entityManager, Request $request, Security $security): Response
{
    $user = $security->getUser();
    $post = $entityManager->getRepository(Post::class)->find($id);
    

    if (!$post) {
        throw $this->createNotFoundException('Post not found');
    }
    $post->incrementViews();
    $entityManager->flush();
    // Create the comment form
    $comment = new Comment();
    $commentForm = $this->createForm(CommentType::class, $comment);
    $commentForm->handleRequest($request);

    // Handle comment submission
    if ($commentForm->isSubmitted() && $commentForm->isValid()) {
        $comment->setPost($post);
        $comment->setUser($user);
        $entityManager->persist($comment);
        $entityManager->flush();

        // Add the new comment's edit form to the editForms array
        $editForms = [];
        foreach ($post->getComments() as $comment) {
            $editForms[$comment->getId()] = $this->createForm(CommentType::class, $comment)->createView();
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'status' => true,
                'message' => 'Comment added successfully!',
                'commentHtml' => $this->renderView('home/_comment.html.twig', [
                    'comment' => $comment,
                    'editForms' => $editForms,
                    'post' => $post,
                ]),
            ]);
        }

        return $this->redirectToRoute('post_detail', ['id' => $post->getId()]);
    }

    // Populate editForms for all comments
    $editForms = [];
    foreach ($post->getComments() as $comment) {
        $editForms[$comment->getId()] = $this->createForm(CommentType::class, $comment)->createView();
    }

    // Render the page
    return $this->render('home/poste.html.twig', [
        'post' => $post,
        'commentForm' => $commentForm->createView(),
        'editForms' => $editForms,
    ]);
}
#[Route('/comment/delete/{id}', name: 'delete_comment', methods: ['POST'])]
public function delete(int $id, Request $request, CommentRepository $commentRepository, EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $comment = $commentRepository->find($id);
        
        if (!$comment || $comment->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
    
        $token = new CsrfToken('delete_comment' . $comment->getId(), $request->request->get('_token'));
        if (!$csrfTokenManager->isTokenValid($token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }
    
        $entityManager->remove($comment);
        $entityManager->flush();
    
        return $this->redirectToRoute('post_detail', ['id' => $comment->getPost()->getId()]);
    }
#[Route('/comment/edit/{id}', name: 'edit_comment', methods: ['POST'])]
public function editInline( Comment $comment, Request $request, EntityManagerInterface $entityManager,Security $security): Response
    {
        // Ensure user is logged in
        $user = $security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to edit comments.');
        }

        // Ensure user is the owner of the comment
        if ($comment->getUser() !== $user) {
            throw $this->createAccessDeniedException('You do not have permission to edit this comment.');
        }

    
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Comment updated successfully.');
        }
    
        return $this->redirectToRoute('post_detail', ['id' => $comment->getPost()->getId()]);
    }    
}

