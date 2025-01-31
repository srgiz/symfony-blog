<?php

declare(strict_types=1);

namespace App\Domain\Blog\ViewModel;

readonly class PostListModel
{
    public function __construct(
        public int $page,
        public int $totalPages,
        public array $posts,
    ) {
    }
}
