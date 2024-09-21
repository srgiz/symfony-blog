<?php

declare(strict_types=1);

namespace App\Core\Blog\Service;

use App\Core\Utils\PaginatorUtils;
use App\Infrastructure\Doctrine\Entity\PostData;
use App\Infrastructure\Doctrine\Repository\PostDataRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class PostPublicService
{
    private PostDataRepository $postRepository;

    public function __construct(EntityManagerInterface $em)
    {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->postRepository = $em->getRepository(PostData::class);
    }

    public function getBySlug(string $slug): ?PostData
    {
        return $this->postRepository->findPublic($slug);
    }

    public function paginate(int $page, int $limit = 2): array
    {
        $blog = $this->postRepository->paginatePublic(PaginatorUtils::offset($limit, $page), $limit);

        return [
            'page' => $page,
            'blog' => $blog,
            'totalPages' => PaginatorUtils::totalPages($limit, $blog->count()),
        ];
    }

    public function search(string $q, int $page, int $limit = 2): array
    {
        $blog = $this->postRepository->searchPublic($q, PaginatorUtils::offset($limit, $page), $limit);

        return [
            'q' => $q,
            'page' => $page,
            'blog' => $blog,
            'totalPages' => PaginatorUtils::totalPages($limit, $blog->count()),
        ];
    }
}
