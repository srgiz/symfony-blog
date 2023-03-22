<?php
declare(strict_types=1);

namespace App\Catalog\Repository;

use App\Doctrine\Query\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategoryTreeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, \App\Catalog\Entity\CategoryTree::class);
    }

    /**
     * @param int[] $categoryIds
     * @return array<int, int> category_id => level
     */
    public function findLevels(array $categoryIds): array
    {
        $result = $this->createQueryBuilder('t', 't.category_id')
            ->select(['t.category_id', 't.level'])
            ->where('t.category_id = t.child_id AND t.category_id IN (:ids)')
            ->setParameter('ids', $categoryIds)
            ->getQuery()
            ->getArrayResult()
        ;

        return array_combine(array_keys($result), array_column($result, 'level'));
    }
}
