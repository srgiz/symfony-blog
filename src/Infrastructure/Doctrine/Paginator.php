<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class Paginator
{
    private \WeakMap $cache;

    public function __construct(
        private readonly Connection $connection,
        private readonly QueryBuilder $queryBuilder,
    ) {
        $this->cache = new \WeakMap();
        $this->cache[$this->queryBuilder] = [];
    }

    public function total(): int
    {
        if (isset($this->cache[$this->queryBuilder]['total'])) {
            return $this->cache[$this->queryBuilder]['total'];
        }

        $queryBuilder = $this->cloneQueryBuilder();
        $queryBuilder->setFirstResult(0)->setMaxResults(null);

        $result = $this->connection->executeQuery(
            sprintf('SELECT COUNT(*) AS dctrn_count FROM (%s) dctrn_table', $queryBuilder->getSQL()),
            $queryBuilder->getParameters(),
            $queryBuilder->getParameterTypes(),
        );

        return $this->cache[$this->queryBuilder]['total'] = $result->fetchNumeric()[0];
    }

    public function get(): array
    {
        return $this->cache[$this->queryBuilder]['items'] ?? ($this->cache[$this->queryBuilder]['items'] = $this->cloneQueryBuilder()->fetchAllAssociative());
    }

    private function cloneQueryBuilder(): QueryBuilder
    {
        return clone $this->queryBuilder;
    }
}
