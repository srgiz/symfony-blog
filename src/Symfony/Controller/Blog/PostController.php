<?php

declare(strict_types=1);

namespace App\Symfony\Controller\Blog;

use App\Domain\Blog\Entity\Id;
use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\UseCase\GetPublicPost\GetPublicPostQuery;
use App\Symfony\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post/{slug}', name: 'post', methods: ['GET'])]
class PostController extends AbstractController
{
    public function __invoke(string $slug): Response
    {
        try {
            $query = new GetPublicPostQuery(new Id($slug));
        } catch (\InvalidArgumentException $e) {
            throw new NotFoundHttpException(previous: $e);
        }

        /** @var Post $post */
        $post = $this->handleCommand($query);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        return $this->render('blog/post.html.twig', ['post' => $post]);
    }
}
