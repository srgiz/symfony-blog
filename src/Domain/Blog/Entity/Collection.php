<?php

declare(strict_types=1);

namespace App\Domain\Blog\Entity;

final class Collection
{
    private int $total;

    public function __construct(
        private readonly array $items,
        ?int $total = null,
    ) {
        $this->total = $total ?? count($this->items);
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotal(): int
    {
        return $this->total;
    }
}
