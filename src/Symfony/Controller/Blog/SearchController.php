<?php

declare(strict_types=1);

namespace App\Symfony\Controller\Blog;

use App\Core\Blog\Service\PostPublicService;
use App\Core\Utils\PaginatorUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/search', name: 'search', methods: ['GET'])]
class SearchController extends AbstractController
{
    public function __construct(private readonly PostPublicService $service)
    {
    }

    public function __invoke(
        #[MapQueryParameter] string $q = '',
        #[MapQueryParameter] string $page = '1',
    ): Response {
        return $this->render('blog/search.html.twig', $this->service->search($q, PaginatorUtils::page($page)));
    }
}
