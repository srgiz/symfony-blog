<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Types;

use App\Domain\Blog\Entity\Id;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class IdType extends AbstractUidType
{
    public const string NAME = 'id';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function getUidClass(): string
    {
        return Id::class;
    }
}
