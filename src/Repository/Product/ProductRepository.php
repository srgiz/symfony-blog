<?php
declare(strict_types=1);

namespace App\Repository\Product;

use App\Entity\Product\Product;
use App\Repository\Product\Query\ProductQueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template T of Product
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function createQueryBuilder($alias, $indexBy = null): ProductQueryBuilder
    {
        return (new ProductQueryBuilder($this->_em))
            ->select($alias)
            ->from($this->_entityName, $alias, $indexBy)
        ;
    }

    /**
     * @param array $filter ['enum' => ['color' => ['red', 'blue']], 'range' => ['width' => [0, 2]]]
     * @return Product[]
     */
    public function findByFilter(array $filter, array $orderBy = null, int $limit = null, int $offset = null): array
    {
        $builder = $this->createQueryBuilder('p')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
        ;

        foreach ($orderBy ?? [] as $sort => $order) {
            $builder->addOrderBy($sort, $order);
        }

        return $builder
            ->addOrderBy('p.id')
            ->andWhereFilter($filter)
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
