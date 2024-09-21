<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class PostStatusType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'enum_status';
    }

    public function getName(): string
    {
        return 'enum_status';
    }
}
