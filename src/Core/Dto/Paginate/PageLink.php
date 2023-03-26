<?php
declare(strict_types=1);

namespace App\Core\Dto\Paginate;

class PageLink
{
    public function __construct(
        public readonly string $url,
        public readonly string $name,
    ) {}
}
