<?php

declare(strict_types=1);

namespace App\Symfony\Controller\Blog;

use App\Domain\Blog\UseCase\GetListPublicPosts\GetListPublicPostsQuery;
use App\Symfony\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'blog', methods: ['GET'])]
class BlogController extends AbstractController
{
    public function __invoke(#[MapQueryParameter] string $page = '1'): Response
    {
        $command = new GetListPublicPostsQuery(max((int) $page, 1));

        return $this->render('blog/blog.html.twig', ['blog' => $this->handleCommand($command)]);
    }
}
