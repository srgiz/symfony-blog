<?php

declare(strict_types=1);

namespace App\Symfony\Controller\Blog;

use App\Domain\Blog\UseCase\SearchPublicPosts\SearchPublicPostsQuery;
use App\Domain\Blog\UseCase\SearchPublicPosts\SearchPublicPostsUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/search', name: 'search', methods: ['GET'])]
class SearchController extends AbstractController
{
    public function __construct(
        private readonly SearchPublicPostsUseCase $useCase,
    ) {
    }

    public function __invoke(
        #[MapQueryParameter] string $q = '',
        #[MapQueryParameter] string $page = '1',
    ): Response {
        $query = new SearchPublicPostsQuery($q, max((int) $page, 1));

        return $this->render('blog/search.html.twig', ['blog' => ($this->useCase)($query), 'q' => $q]);
    }
}
