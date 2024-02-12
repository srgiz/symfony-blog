<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Core\Blog\Enum\StatusEnum;
use App\Core\Entity\Post;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @template T of Post
 * @template-extends EntityRepository<T>
 */
class PostRepository extends EntityRepository
{
    public function findPublic(string $slug): ?Post
    {
        return $this->findOneBy(['slug' => $slug, 'status' => StatusEnum::Active->value]);
    }

    public function paginatePublic(int $offset, int $limit): Paginator
    {
        return new Paginator($this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameters([
                'status' => StatusEnum::Active->value,
            ])
        );
    }

    public function searchPublic(string $q, int $offset, int $limit): Paginator
    {
        return new Paginator($this->createQueryBuilder('p')
            ->andWhere("p.status = :status AND TS_MATCH_VQ(:q, p.title, p.content, COALESCE(p.preview, '')) = true")
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameters([
                'status' => StatusEnum::Active->value,
                'q' => $q,
            ])
        );
    }

    public function paginateAll(int $offset, int $limit): Paginator
    {
        return new Paginator($this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
        );
    }
}
