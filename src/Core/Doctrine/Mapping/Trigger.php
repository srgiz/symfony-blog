<?php
declare(strict_types=1);

namespace App\Core\Doctrine\Mapping;


/**
 * Атрибут для информирования использования триггера
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class Trigger
{
    public function __construct(
        public string $name,
        public ?string $description = null,
    ) {}
}
