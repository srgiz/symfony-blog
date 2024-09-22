<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\SavePost;

use App\Domain\Blog\Dto\EditPostDto;
use App\Domain\Blog\Entity\Id;
use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Repository\PostRepositoryInterface;

readonly class SavePostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
    ) {
    }

    public function __invoke(EditPostDto $command): void
    {
        $post = new Post(
            id: $command->id ?? new Id(),
            status: Post\Status::from($command->status),
            title: $command->title,
            content: $command->content,
            preview: $command->preview,
        );

        $this->postRepository->save($post);
    }
}
