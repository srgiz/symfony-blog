<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Blog\Entity\Id;
use App\Domain\Blog\Entity\Post\Status;
use App\Infrastructure\Doctrine\Entity\PostData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function findPublic(Id $id): ?PostData
    {
        return $this->createPublicQuery()
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()[0]
            ?? null
        ;
    }

    public function paginatePublic(int $offset, int $limit): Paginator
    {
        return new Paginator($this->createPublicQuery($offset, $limit));
    }

    public function searchPublic(string $q, int $offset, int $limit): Paginator
    {
        return new Paginator($this->createPublicQuery($offset, $limit)
            ->andWhere("TS_MATCH_VQ(:q, p.title, p.content, COALESCE(p.preview, '')) = true")
            ->setParameter('q', $q)
        );
    }

    private function createPublicQuery(int $offset = 0, int $limit = 1): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameter('status', Status::Active)
        ;
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
