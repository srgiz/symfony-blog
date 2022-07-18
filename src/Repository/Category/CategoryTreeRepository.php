<?php
declare(strict_types=1);

namespace App\Repository\Category;

use App\Doctrine\Query\ServiceEntityRepository;
use App\Entity\Category\CategoryTree;
use Doctrine\Persistence\ManagerRegistry;

class CategoryTreeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryTree::class);
    }
}
