<?php
declare(strict_types=1);

namespace App\Catalog\Entity;

use App\Catalog\Repository\CategoryTreeRepository;
use App\Core\Doctrine\Mapping\Trigger;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryTreeRepository::class)]
#[ORM\Index(columns: ['category_id'], name: 'category_id')]
#[ORM\Index(columns: ['child_id'], name: 'child_id')]
#[ORM\UniqueConstraint(name: 'unique_row', columns: ['category_id', 'child_id'])]
#[Trigger('change_parent_category', '@see Category')]
class CategoryTree
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $categoryId = null;

    #[ORM\Column(type: 'integer')]
    private ?int $childId = null;

    #[ORM\Column(type: 'integer')]
    private ?int $level = null;

    #[ORM\ManyToOne(targetEntity: Category::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Category $category = null;

    #[ORM\ManyToOne(targetEntity: Category::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(name: 'child_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Category $child = null;
}
