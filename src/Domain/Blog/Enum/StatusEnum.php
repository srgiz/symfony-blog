<?php
declare(strict_types=1);

namespace App\Domain\Blog\Enum;

enum StatusEnum: string
{
    case Draft = 'draft';

    case Active = 'active';
}
