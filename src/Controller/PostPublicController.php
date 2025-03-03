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
public function index(EntityManagerInterface $entityManager, Request $request): Response
{
    $postsPerPage = 1;
    $currentPage = $request->query->getInt('page', 1);
    
    $repository = $entityManager->getRepository(Post::class);
    
    
    $totalPosts = $repository->createQueryBuilder('p')
        ->select('COUNT(p.id)')
        ->getQuery()
        ->getSingleScalarResult();
    
    $totalPages = ceil($totalPosts / $postsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $postsPerPage;
    
    $posts = $repository->findBy(
        [],           
        null,        
        $postsPerPage, 
        $offset        
    );

    return $this->render('home/compagne.html.twig', [
        'posts' => $posts,
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'postsPerPage' => $postsPerPage
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

    // Get locale from query parameter or session, fallback to default 'fr' from config
    $locale = $request->query->get('locale', $request->getSession()->get('_locale', 'fr'));
    $request->setLocale($locale);
    $request->getSession()->set('_locale', $locale); // Persist locale in session

    $post->incrementViews();
    $entityManager->flush();
   
    $comment = new Comment();
    $commentForm = $this->createForm(CommentType::class, $comment);
    $commentForm->handleRequest($request);

    if ($commentForm->isSubmitted() && $commentForm->isValid()) {
        $comment->setPost($post);
        $comment->setUser($user);
        $entityManager->persist($comment);
        $entityManager->flush();

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

        return $this->redirectToRoute('post_detail', [
            'id' => $post->getId(),
            'locale' => $locale,
        ]);
    }

    $editForms = [];
    foreach ($post->getComments() as $comment) {
        $editForms[$comment->getId()] = $this->createForm(CommentType::class, $comment)->createView();
    }

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
        
        $user = $security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to edit comments.');
        }

        
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

