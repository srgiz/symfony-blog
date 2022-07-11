<?php
declare(strict_types=1);

namespace App\Repository\Product;

use App\Entity\Product\ProductAttributeValue;
use App\Repository\Product\Query\ProductAttributeValueQueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductAttributeValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductAttributeValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductAttributeValue[]    findAll()
 * @method ProductAttributeValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductAttributeValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductAttributeValue::class);
    }

    public function createQueryBuilder($alias, $indexBy = null): ProductAttributeValueQueryBuilder
    {
        return (new ProductAttributeValueQueryBuilder($this->_em))
            ->select($alias)
            ->from($this->_entityName, $alias, $indexBy)
        ;
    }
}
