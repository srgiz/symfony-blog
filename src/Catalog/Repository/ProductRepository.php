<?php
declare(strict_types=1);

namespace App\Catalog\Repository;

use App\Catalog\Entity\Product;
use App\Catalog\Entity\ProductAttributeValue;
use App\Core\Doctrine\Query\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    private ProductAttributeValueRepository $productAttributeValueRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);

        /** @var ProductAttributeValueRepository $productAttributeValueRepository */
        $productAttributeValueRepository = $registry->getRepository(ProductAttributeValue::class);
        $this->productAttributeValueRepository = $productAttributeValueRepository;
    }

    public function findById(int $id): ?Product
    {
        return $this->createQueryBuilder('p')->where("p.id = $id")->getQuery()->getOneOrNullResult();
    }

    /**
     * @param array<string, mixed> $filter ['enum' => ['color' => ['red', 'blue']], 'range' => ['width' => [0, 2]]]
     * @param array<string, string> $orderBy
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
            ->andWhereExists(
                $this->productAttributeValueRepository->createQueryBuilder('filter_values')
                    ->select('1')
                    ->andWhereFilter($filter)
                    ->andWhere('p.id = filter_values.product_id')
            )
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
