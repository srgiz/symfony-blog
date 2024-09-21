<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\GetPublicPosts;

readonly class GetPublicPostsCommand
{
    public function __construct(
        public int $page,
    ) {
    }
}
