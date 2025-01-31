<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\DeletePost;

use App\Domain\Blog\Entity\Id;

readonly class DeletePostEvent
{
    public function __construct(
        public Id $id,
    ) {
    }
}
