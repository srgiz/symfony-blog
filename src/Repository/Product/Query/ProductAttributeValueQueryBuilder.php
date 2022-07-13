<?php
declare(strict_types=1);

namespace App\Repository\Product\Query;

use App\Doctrine\Query\QueryBuilder;

class ProductAttributeValueQueryBuilder extends QueryBuilder
{
    private const FILTER_TYPE_ENUM = 'enum';
    private const FILTER_TYPE_RANGE = 'range';

    /**
     * @param array $filter ['enum' => ['color' => ['red', 'blue']], 'range' => ['width' => [0, 2]]]
     * @example jsonb_exists_any(jsonb_object_field(v.values, 'color'), array['red', 'blue']) and (to_real(jsonb_extract_path_text(v.values, 'width', '0')) between 0 and 2)
     */
    public function andWhereFilter(array $filter): static
    {
        $alias = $this->getRootAliases()[0];

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
                $paramCode = $this->createFilterParameterName($hashCode, $type, 'name');

                if (null !== $this->getParameter($paramCode)) {
                    // уже есть фильтрация
                    continue;
                }

                switch ($type) {
                    case self::FILTER_TYPE_ENUM:
                        $paramValues = $this->createFilterParameterName($hashCode, $type, 'values');

                        $this
                            ->andWhere("JSONB_EXISTS_ANY(JSONB_OBJECT_FIELD({$alias}.values, :{$paramCode}), ARRAY_TEXT(:{$paramValues})) = true")
                            ->setParameter($paramCode, $code)
                            ->setParameter($paramValues, $values)
                        ;

                        break;

                    case self::FILTER_TYPE_RANGE:
                        $paramMin = $this->createFilterParameterName($hashCode, $type, 'min');
                        $paramMax = $this->createFilterParameterName($hashCode, $type, 'max');

                        $whereRange = "TO_REAL(JSONB_EXTRACT_PATH_TEXT({$alias}.values, :{$paramCode}, '0'))";

                        $min = isset($values[0]) ? (float)$values[0] : null;
                        $max = isset($values[1]) ? (float)$values[1] : null;

                        if (null !== $min && null !== $max) {
                            $whereRange .= " BETWEEN :{$paramMin} AND :{$paramMax}";

                            $this
                                ->andWhere($whereRange)
                                ->setParameter($paramCode, $code)
                                ->setParameter($paramMin, $min)
                                ->setParameter($paramMax, $max)
                            ;
                        } else if (null === $min) {
                            $whereRange .= " <= :{$paramMax}";

                            $this
                                ->andWhere($whereRange)
                                ->setParameter($paramCode, $code)
                                ->setParameter($paramMax, $max)
                            ;
                        } else if (null === $max) {
                            $whereRange .= " >= :{$paramMin}";

                            $this
                                ->andWhere($whereRange)
                                ->setParameter($paramCode, $code)
                                ->setParameter($paramMin, $min)
                            ;
                        }

                        break;
                }
            }
        }

        return $this;
    }

    private function createFilterParameterName(string $code, string $type, string $name): string
    {
        return sprintf('pv_%s_%s_%s', $type, $name, $code);
    }
}
