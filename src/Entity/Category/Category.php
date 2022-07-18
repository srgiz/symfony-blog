<?php
declare(strict_types=1);

namespace App\Entity\Category;

use App\Repository\Category\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Index(columns: ['parent_id'], name: 'parent_id')]
#[ORM\UniqueConstraint(name: 'uid', columns: ['uid'])]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 128)]
    private ?int $uid = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $parentId = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: self::class, fetch: 'EXTRA_LAZY', inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?self $parent;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, fetch: 'EXTRA_LAZY')]
    private iterable $children;
}
