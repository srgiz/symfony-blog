<?php
declare(strict_types=1);

namespace App\Core\Blog\Service;

use App\Core\Entity\Post;
use App\Core\Repository\PostRepository;
use App\Core\Utils\PaginatorUtils;
use Doctrine\ORM\EntityManagerInterface;

readonly class PostPublicService
{
    private PostRepository $postRepository;

    public function __construct(EntityManagerInterface $em)
    {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->postRepository = $em->getRepository(Post::class);
    }

    public function getBySlug(string $slug): ?Post
    {
        return $this->postRepository->findPublic($slug);
    }

    public function paginate(int $page, int $limit = 1): array
    {
        $blog = $this->postRepository->paginatePublic(PaginatorUtils::offset($limit, $page), $limit);

        return [
            'page' => $page,
            'blog' => $blog,
            'totalPages' => PaginatorUtils::totalPages($limit, $blog->count()),
        ];
    }

    public function search(string $q, int $page, int $limit = 1): array
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
