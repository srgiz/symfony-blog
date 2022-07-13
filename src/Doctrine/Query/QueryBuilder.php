<?php
declare(strict_types=1);

namespace App\Doctrine\Query;

use Doctrine\ORM;

class QueryBuilder extends ORM\QueryBuilder
{
    /**
     * @param ORM\QueryBuilder $subQueryBuilder Вложенный запрос
     * @return $this
     */
    public function andWhereExists(ORM\QueryBuilder $subQueryBuilder): static
    {
        /**
         * Переносим параметры в основной запрос
         * @var ORM\Query\Parameter $parameter
         */
        foreach ($subQueryBuilder->getParameters() as $parameter) {
            $this->setParameter($parameter->getName(), $parameter->getValue(), $parameter->getType());
        }

        return $this->andWhere($this->expr()->exists($subQueryBuilder->getDQL()));
    }
}
