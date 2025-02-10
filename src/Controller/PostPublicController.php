<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;

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

}
