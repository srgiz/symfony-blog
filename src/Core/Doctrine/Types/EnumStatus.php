<?php

namespace App\Core\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EnumStatus extends Type
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
