<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\GetPublicPost;

use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Repository\PostRepositoryInterface;

readonly class GetPublicPostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
    ) {
    }

    public function __invoke(GetPublicPostQuery $query): ?Post
    {
        return $this->postRepository->findPublic($query->id);
    }
}
