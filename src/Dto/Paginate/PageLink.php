<?php
declare(strict_types=1);

namespace App\Dto\Paginate;

class PageLink
{
    public function __construct(
        public readonly string $url,
        public readonly string $name,
    ) {}
}
