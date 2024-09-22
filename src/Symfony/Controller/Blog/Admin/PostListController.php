<?php

declare(strict_types=1);

namespace App\Symfony\Controller\Blog\Admin;

use App\Domain\Blog\UseCase\GetAllPosts\GetAllPostsQuery;
use App\Domain\Blog\UseCase\GetAllPosts\GetAllPostsUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/blog', name: 'admin-post-list', methods: ['GET'])]
class PostListController extends AbstractController
{
    public function __construct(
        private readonly GetAllPostsUseCase $useCase,
    ) {
    }

    public function __invoke(#[MapQueryParameter] string $page = '1'): Response
    {
        $query = new GetAllPostsQuery(max((int) $page, 1));

        return $this->render('blog/admin/post-list.html.twig', ['blog' => ($this->useCase)($query)]);
    }
}
