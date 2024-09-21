<?php

declare(strict_types=1);

namespace App\Domain\Blog\Dto;

readonly class PostListDto
{
    public function __construct(
        public int $page,
        public int $totalPages,
        public array $posts,
    ) {
    }
}
