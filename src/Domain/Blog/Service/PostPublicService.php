<?php
declare(strict_types=1);

namespace App\Domain\Blog\Service;

use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class PostPublicService
{
    private PostRepository $postRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->postRepository = $em->getRepository(Post::class);
    }

    public function getBySlug(string $slug): ?Post
    {
        return $this->postRepository->findPublic($slug);
    }

    public function paginate(int $page, int $limit = 1): array
    {
        $page = max($page, 1);
        $offset = intval(($page - 1) * $limit);
        $blog = $this->postRepository->paginatePublic($offset, $limit);

        return [
            'page' => $page,
            'blog' => $blog,
            'totalPages' => (int)ceil($blog->count() / $limit),
        ];
    }

    public function search(string $q, int $page, int $limit = 1): array
    {
        $page = max($page, 1);
        $offset = intval(($page - 1) * $limit);
        $blog = $this->postRepository->searchPublic($q, $offset, $limit);

        return [
            'q' => $q,
            'page' => $page,
            'blog' => $blog,
            'totalPages' => (int)ceil($blog->count() / $limit),
        ];
    }
}
