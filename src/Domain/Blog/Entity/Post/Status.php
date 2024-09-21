<?php

declare(strict_types=1);

namespace App\Domain\Blog\Entity\Post;

enum Status: string
{
    case Draft = 'draft';

    case Active = 'active';
}
