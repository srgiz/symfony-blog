<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\GetPublicPost;

use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Message\TestMessage;
use App\Domain\Blog\Repository\PostRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class GetPublicPostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private MessageBusInterface $bus, // todo: to Infr
    ) {
    }

    public function __invoke(GetPublicPostQuery $query): ?Post
    {
        $this->bus->dispatch(new TestMessage(uniqid('val')));

        return $this->postRepository->findPublic($query->id);
    }
}
