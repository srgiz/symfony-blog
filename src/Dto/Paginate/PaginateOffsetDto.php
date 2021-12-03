<?php
declare(strict_types=1);

namespace App\Dto\Paginate;

class PaginateOffsetDto
{
    public function __construct(
        private int $offset,
        private int $limit,
        private int $total,
        private iterable $items,
        private ?int $prevOffset = null,
        private ?int $nextOffset = null,
    ) {}

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getItems(): iterable
    {
        return $this->items;
    }

    public function getPrevOffset(): ?int
    {
        return $this->prevOffset;
    }

    public function getNextOffset(): ?int
    {
        return $this->nextOffset;
    }
}
