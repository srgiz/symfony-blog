<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\SavePost;

use App\Domain\Blog\Entity\Post;

readonly class SavePostEvent
{
    public function __construct(
        public Post $post,
    ) {
    }
}
