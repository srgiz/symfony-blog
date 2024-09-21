<?php

declare(strict_types=1);

namespace App\Core\Blog\Service;

use App\Core\Utils\PaginatorUtils;
use App\Infrastructure\Doctrine\Entity\PostData;
use App\Infrastructure\Doctrine\Repository\PostDataRepository;
use Doctrine\ORM\EntityManagerInterface;

class PostManager
{
    private PostDataRepository $postRepository;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->postRepository = $em->getRepository(PostData::class);
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

    public function getById(int $id): ?PostData
    {
        return $this->postRepository->findOneBy(['id' => $id]);
    }

    public function edit(PostData $post): bool
    {
        if (!$this->em->getUnitOfWork()->isInIdentityMap($post)) {
            $this->em->persist($post);
        }

        $this->em->flush();

        return true;
    }

    public function deleteById(int $id): void
    {
        if ($post = $this->em->getReference(PostData::class, $id)) {
            $this->em->remove($post);
            $this->em->flush();
        }
    }
}
