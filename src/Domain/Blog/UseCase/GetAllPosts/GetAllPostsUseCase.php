<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\GetAllPosts;

use App\Domain\Blog\Dto\PostListDto;
use App\Domain\Blog\Repository\PostRepositoryInterface;

readonly class GetAllPostsUseCase
{
    private const int LIMIT = 1;

    public function __construct(
        private PostRepositoryInterface $postRepository,
    ) {
    }

    public function __invoke(GetAllPostsQuery $query): PostListDto
    {
        $collection = $this->postRepository->paginateAll($query->page, self::LIMIT);

        return new PostListDto(
            page: $query->page,
            totalPages: $collection->getTotal(),
            posts: $collection->getItems(),
        );
    }
}
