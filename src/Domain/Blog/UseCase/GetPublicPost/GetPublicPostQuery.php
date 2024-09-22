<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\GetPublicPost;

use App\Domain\Blog\Entity\Id;

readonly class GetPublicPostQuery
{
    public function __construct(
        public Id $id,
    ) {
    }
}
