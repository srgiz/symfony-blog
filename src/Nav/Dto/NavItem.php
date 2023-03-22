<?php
declare(strict_types=1);

namespace App\Nav\Dto;

class NavItem
{
    /** @var array<static> */
    private array $items = [];

    public function __construct(
        public readonly string $url,
        public readonly string $name,
    ) {}

    /**
     * @return array<static>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(NavItem $item): void
    {
        $this->items[] = $item;
    }
}
