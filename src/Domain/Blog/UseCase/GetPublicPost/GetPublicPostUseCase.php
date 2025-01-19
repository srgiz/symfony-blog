<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\GetPublicPost;

use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Message\TestMessage;
use App\Domain\Blog\Repository\PostRepositoryInterface;
use App\Domain\Messenger\MessageBusInterface;

readonly class GetPublicPostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private MessageBusInterface $bus,
    ) {
    }

    public function __invoke(GetPublicPostQuery $query): ?Post
    {
        $this->bus->send(new TestMessage(uniqid('val')));

        return $this->postRepository->findPublic($query->id);
    }
}
