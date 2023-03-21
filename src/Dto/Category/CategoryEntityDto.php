<?php
declare(strict_types=1);

namespace App\Dto\Category;

use App\Entity\Category\Category;

class CategoryEntityDto implements CategoryInterface
{
    public function __construct(
        protected Category $category
    ) {}

    public function getId(): ?int
    {
        return $this->category->getId();
    }

    public function getUid(): ?string
    {
        return $this->category->getUid();
    }

    public function getParentId(): ?int
    {
        return $this->category->getParentId();
    }

    public function getName(): ?string
    {
        return $this->category->getName();
    }

    public function hasChildren(): bool
    {
        return !empty($this->getChildren());
    }

    public function getChildren(): array
    {
        return [];
    }
}
