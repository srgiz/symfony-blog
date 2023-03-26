<?php
declare(strict_types=1);

namespace App\Core\Doctrine\Mapping;


/**
 * Атрибут для информирования использования триггера
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Trigger
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
    ) {}
}
