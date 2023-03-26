<?php
declare(strict_types=1);

namespace App\Core\Doctrine\Query;

use Doctrine\Bundle\DoctrineBundle;

abstract class ServiceEntityRepository extends DoctrineBundle\Repository\ServiceEntityRepository
{
    public function createQueryBuilder($alias, $indexBy = null): QueryBuilder
    {
        return (new QueryBuilder($this->_em))
            ->select($alias)
            ->from($this->_entityName, $alias, $indexBy)
        ;
    }
}
