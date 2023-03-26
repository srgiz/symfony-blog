<?php
declare(strict_types=1);

namespace App\Nav\Dto;

final class NavItem
{
    /** @var array<self> */
    private array $items = [];

    public function __construct(
        public readonly string $url,
        public readonly string $name,
    ) {}

    /**
     * @return array<self>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(self $item): void
    {
        $this->items[] = $item;
    }
}
