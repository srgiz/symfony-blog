<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\DeletePost;

use App\Domain\Blog\Repository\PostRepositoryInterface;

readonly class DeletePostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
    ) {
    }

    public function __invoke(DeletePostCommand $command): void
    {
        $this->postRepository->delete($command->id);
    }
}
