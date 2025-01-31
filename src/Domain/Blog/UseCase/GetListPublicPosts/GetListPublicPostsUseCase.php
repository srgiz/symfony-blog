<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\GetListPublicPosts;

use App\Domain\Blog\Repository\PostRepositoryInterface;
use App\Domain\Blog\ViewModel\PostListModel;

final readonly class GetListPublicPostsUseCase
{
    private const int LIMIT = 1;

    public function __construct(
        private PostRepositoryInterface $postRepository,
    ) {
    }

    public function __invoke(GetListPublicPostsQuery $query): PostListModel
    {
        $collection = $this->postRepository->paginatePublic($query->page, self::LIMIT);

        return new PostListModel(
            page: $query->page,
            totalPages: $collection->getTotal(),
            posts: $collection->getItems(),
        );
    }
}
