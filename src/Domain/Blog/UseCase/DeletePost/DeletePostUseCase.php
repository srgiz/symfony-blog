<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\DeletePost;

use App\Domain\Blog\Repository\PostRepositoryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

readonly class DeletePostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(DeletePostCommand $command): void
    {
        $this->postRepository->delete($command->id);
        $this->eventDispatcher->dispatch(new DeletePostEvent($command->id));
    }
}
