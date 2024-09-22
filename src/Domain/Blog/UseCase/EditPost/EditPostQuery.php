<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\EditPost;

use App\Domain\Blog\Entity\Id;

readonly class EditPostQuery
{
    public function __construct(
        public ?Id $id,
    ) {
    }
}
