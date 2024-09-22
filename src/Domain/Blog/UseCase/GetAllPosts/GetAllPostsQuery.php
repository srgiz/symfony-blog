<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\GetAllPosts;

readonly class GetAllPostsQuery
{
    public function __construct(
        public int $page,
    ) {
    }
}
