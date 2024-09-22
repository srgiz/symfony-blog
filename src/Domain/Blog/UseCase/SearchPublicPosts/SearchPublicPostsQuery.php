<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\SearchPublicPosts;

readonly class SearchPublicPostsQuery
{
    public function __construct(
        public string $q,
        public int $page,
    ) {
    }
}
