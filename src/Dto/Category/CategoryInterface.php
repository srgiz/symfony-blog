<?php

namespace App\Dto\Category;

interface CategoryInterface
{
    public function getId(): ?int;

    public function getUid(): ?string;

    public function getParentId(): ?int;

    public function getName(): ?string;

    public function hasChildren(): bool;

    public function getChildren(): array;
}
