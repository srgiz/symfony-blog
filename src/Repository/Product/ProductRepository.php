<?php
declare(strict_types=1);

namespace App\Repository\Product;

use App\Entity\Product\Product;
use App\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    /**
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

        $builder->addOrderBy('p.id');
        $this->filterAttributes($builder, $filter);

        return $builder->getQuery()->getArrayResult();
    }

    /**
     * @param QueryBuilder $builder
     * @param array $filter ['enum' => ['color' => ['red', 'blue']], 'range' => ['width' => [0, 2]]]
     * @example jsonb_exists_any(jsonb_object_field(v.values, 'color'), array['red']) and (jsonb_extract_path_text(v.values, 'width', '0')::real between 0 and 2)
     */
    private function filterAttributes(QueryBuilder $builder, array $filter): void
    {
        $alias = 'product_values';

        if (!$this->hasJoin($builder, $alias)) {
            $join = $builder->getRootAliases()[0].'.values';
            $builder->join($join, $alias);
        }

        foreach ($filter as $type => $props) {
            foreach ($props as $code => $values) {
                if (!is_array($values)) {
                    $values = [$values];
                }

                if (empty($values)) {
                    // не передан фильтр
                    continue;
                }

                $hashCode = hash('crc32c', $code);
                $paramCode = sprintf('pv_%s_name_%s', $type, $hashCode);

                if (null !== $builder->getParameter($paramCode)) {
                    // уже есть фильтрация
                    continue;
                }

                switch ($type) {
                    case 'enum':
                        $paramValues = sprintf('pv_%s_values_%s', $type, $hashCode);

                        $builder
                            ->andWhere("JSONB_EXISTS_ANY(JSONB_OBJECT_FIELD({$alias}.values, :{$paramCode}), ARRAY_TEXT(:{$paramValues})) = true")
                            ->setParameter($paramCode, $code)
                            ->setParameter($paramValues, $values)
                        ;

                        break;

                    case 'range':
                        $paramMin = sprintf('pv_%s_min_%s', $type, $hashCode);
                        $paramMax = sprintf('pv_%s_max_%s', $type, $hashCode);

                        $whereRange = "JSONB_EXTRACT_PATH_TEXT('real', {$alias}.values, :{$paramCode}, '0')";

                        $min = isset($values[0]) ? (float)$values[0] : null;
                        $max = isset($values[1]) ? (float)$values[1] : null;

                        if (null !== $min && null !== $max) {
                            $whereRange .= " BETWEEN :{$paramMin} AND :{$paramMax}";

                            $builder
                                ->andWhere($whereRange)
                                ->setParameter($paramCode, $code)
                                ->setParameter($paramMin, $min)
                                ->setParameter($paramMax, $max)
                            ;
                        } else if (null === $min) {
                            $whereRange .= " <= :{$paramMax}";

                            $builder
                                ->andWhere($whereRange)
                                ->setParameter($paramCode, $code)
                                ->setParameter($paramMax, $max)
                            ;
                        } else if (null === $max) {
                            $whereRange .= " >= :{$paramMin}";

                            $builder
                                ->andWhere($whereRange)
                                ->setParameter($paramCode, $code)
                                ->setParameter($paramMin, $min)
                            ;
                        }

                        break;
                }
            }
        }
    }
}
