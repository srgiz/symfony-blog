<?php

declare(strict_types=1);

namespace App\Core\Blog\Service;

use App\Core\Entity\Post;
use App\Core\Repository\PostRepository;
use App\Core\Utils\PaginatorUtils;
use Doctrine\ORM\EntityManagerInterface;

class PostManager
{
    private PostRepository $postRepository;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->postRepository = $em->getRepository(Post::class);
    }

    public function paginate(int $page, int $limit = 2): array
    {
        $blog = $this->postRepository->paginateAll(PaginatorUtils::offset($limit, $page), $limit);

        return [
            'page' => $page,
            'blog' => $blog,
            'totalPages' => PaginatorUtils::totalPages($limit, $blog->count()),
        ];
    }

    public function getById(int $id): ?Post
    {
        return $this->postRepository->findOneBy(['id' => $id]);
    }

    public function edit(Post $post): bool
    {
        if (!$this->em->getUnitOfWork()->isInIdentityMap($post)) {
            $this->em->persist($post);
        }

        $this->em->flush();
        return true;
    }

    public function deleteById(int $id): void
    {
        if ($post = $this->em->getReference(Post::class, $id)) {
            $this->em->remove($post);
            $this->em->flush();
        }
    }
}
