<?php
declare(strict_types=1);

namespace App\Catalog\Category;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;

readonly class CategoryTreeBuilder
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    /**
     * Пересоздание связей дерева категорий
     */
    public function rebuild(): void
    {
        $this->em->createNativeQuery('CALL rebuild_category_tree()', new ResultSetMapping())->execute();
    }
}
