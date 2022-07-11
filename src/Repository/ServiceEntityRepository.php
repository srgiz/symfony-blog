<?php
declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository as DoctrineServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

/**
 * @template T of object
 * @template-extends DoctrineServiceEntityRepository<T>
 * @todo Сделать Attribute для исключения из контейнера
 */
abstract class ServiceEntityRepository extends DoctrineServiceEntityRepository
{
    protected function hasJoin(QueryBuilder $builder, string $aliasJoin, int $rootIndex = 0): bool
    {
        $aliasTable = $builder->getRootAliases()[$rootIndex];
        $parts = $builder->getDQLPart('join')[$aliasTable] ?? [];

        if (empty($parts)) {
            return false;
        }

        /** @var Join $join */
        foreach ($parts as $join) {
            if ($join->getAlias() === $aliasJoin) {
                return true;
            }
        }

        return false;
    }
}
