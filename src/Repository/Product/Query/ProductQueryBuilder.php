<?php
declare(strict_types=1);

namespace App\Repository\Product\Query;

use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;

class ProductQueryBuilder extends QueryBuilder
{
    /**
     * Вложенный запрос
     */
    public function andWhereExists(QueryBuilder $subQueryBuilder): static
    {
        /**
         * Переносим параметры в основной запрос
         * @var Parameter $parameter
         */
        foreach ($subQueryBuilder->getParameters() as $parameter) {
            $this->setParameter($parameter->getName(), $parameter->getValue(), $parameter->getType());
        }

        return $this->andWhere($this->expr()->exists($subQueryBuilder->getDQL()));
    }
}
