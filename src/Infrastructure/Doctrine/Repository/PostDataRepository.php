<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Blog\Entity\Post\Status;
use App\Infrastructure\Doctrine\Entity\PostData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template T of PostData
 *
 * @template-extends ServiceEntityRepository<T>
 */
class PostDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostData::class);
    }

    public function findPublic(string $slug): ?PostData
    {
        return $this->findOneBy(['slug' => $slug, 'status' => Status::Active->value]);
    }

    public function paginatePublic(int $offset, int $limit): Paginator
    {
        return new Paginator($this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameter('status', Status::Active->value)
        );
    }

    public function searchPublic(string $q, int $offset, int $limit): Paginator
    {
        return new Paginator($this->createQueryBuilder('p')
            ->andWhere("p.status = :status AND TS_MATCH_VQ(:q, p.title, p.content, COALESCE(p.preview, '')) = true")
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameter('status', Status::Active->value)
            ->setParameter('q', $q)
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
