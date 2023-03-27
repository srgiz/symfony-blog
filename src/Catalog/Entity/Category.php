<?php
declare(strict_types=1);

namespace App\Catalog\Entity;

use App\Catalog\Repository\CategoryRepository;
use App\Core\Doctrine\Mapping\Trigger;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Index(columns: ['parent_id'], name: 'parent_id')]
#[ORM\UniqueConstraint(name: 'uid', columns: ['uid'])]
#[Trigger('change_parent_category')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 128)]
    private ?string $uid = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $parentId = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: self::class, fetch: 'EXTRA_LAZY', inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, fetch: 'EXTRA_LAZY')]
    private Collection $children;

    /** @var self[] То же самое что и $children, только заполняется отдельно от doctrine */
    private array $childCategories = [];

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function hasChildCategories(): bool
    {
        return !empty($this->childCategories);
    }

    /** @return self[] */
    public function getChildCategories(): array
    {
        return $this->childCategories;
    }

    public function addChildCategory(self $category): self
    {
        $this->childCategories[] = $category;
        return $this;
    }
}
