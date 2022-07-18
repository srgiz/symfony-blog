<?php
declare(strict_types=1);

namespace App\Repository\Category;

use App\Doctrine\Query\ServiceEntityRepository;
use App\Entity\Category\Category;
use Doctrine\Persistence\ManagerRegistry;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }
}
