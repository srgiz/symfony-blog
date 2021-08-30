<?php
declare(strict_types=1);

namespace App\Repository\Product;

use App\Entity\Product\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param QueryBuilder $builder
     * @param array $filter ['color' => ['red', 'blue']]
     */
    private function filterAttributes(QueryBuilder $builder, array $filter): void
    {
        $join = $builder->getRootAliases()[0].'.values';
        $alias = 'product_values';
        $builder->join($join, $alias);

        foreach ($filter as $code => $values) {
            if (!is_array($values)) {
                $values = [$values];
            }

            $key = 'pv_' . hash('crc32c', $code);
            $keyValues = $key . '_array';

            $builder
                ->andWhere("JSONB_EXISTS_ANY(JSONB_EXTRACT({$alias}.values, :{$key}), ARRAY_TEXT(:{$keyValues})) = true")
                ->setParameter($key, $code)
                ->setParameter($keyValues, $values)
            ;
        }
    }
}
