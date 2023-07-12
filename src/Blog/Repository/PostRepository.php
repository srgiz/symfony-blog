<?php
declare(strict_types=1);

namespace App\Blog\Repository;

use App\Blog\Entity\Post;
use App\Blog\Enum\StatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

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
