<?php
declare(strict_types=1);

namespace App\Blog\Controller;

use App\Blog\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post/{slug}', name: 'post', methods: ['GET'])]
class PostController extends AbstractController
{
    public function __construct(private readonly PostRepository $postRepository) {}

    public function __invoke(string $slug): Response
    {
        $post = $this->postRepository->findPublic($slug);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        return $this->render('blog/post.html.twig', ['post' => $post]);
    }
}
