<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\SavePost;

use App\Domain\Blog\Entity\Id;

readonly class SavePostModel
{
    public function __construct(
        public Id $id,
    ) {
    }
}
