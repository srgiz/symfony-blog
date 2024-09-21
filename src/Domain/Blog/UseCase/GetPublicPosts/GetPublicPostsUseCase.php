<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\GetPublicPosts;

use App\Domain\Blog\Dto\PostListDto;
use App\Domain\Blog\Repository\PostRepositoryInterface;

final readonly class GetPublicPostsUseCase
{
    private const int LIMIT = 1;

    public function __construct(
        private PostRepositoryInterface $postRepository,
    ) {
    }

    public function __invoke(GetPublicPostsCommand $command): PostListDto
    {
        $collection = $this->postRepository->paginatePublic($command->page, self::LIMIT);

        return new PostListDto(
            page: $command->page,
            totalPages: $collection->getTotal(),
            posts: $collection->getItems(),
        );
    }
}
