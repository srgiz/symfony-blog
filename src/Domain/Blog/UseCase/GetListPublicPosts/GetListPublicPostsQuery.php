<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\GetListPublicPosts;

readonly class GetListPublicPostsQuery
{
    public function __construct(
        public int $page,
    ) {
    }
}
