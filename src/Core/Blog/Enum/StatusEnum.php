<?php
declare(strict_types=1);

namespace App\Core\Blog\Enum;

enum StatusEnum: string
{
    case Draft = 'draft';

    case Active = 'active';
}
