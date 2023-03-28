<?php
declare(strict_types=1);

namespace App\Core\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class Csrf
{
    public function __construct(
        public string $id,
        public string $field = '_csrf_token',
    ) {}
}
