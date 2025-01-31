<?php

declare(strict_types=1);

namespace App\Infrastructure\Logger;

use App\Domain\Blog\UseCase\CreateUser\CreateUserEvent;
use App\Domain\Blog\UseCase\DeletePost\DeletePostEvent;
use App\Domain\Blog\UseCase\SavePost\SavePostEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

readonly class LogListener
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    #[AsEventListener]
    public function onUserCreate(CreateUserEvent $event): void
    {
        $this->logger->notice('CreateUser: id = {id}', [
            'id' => $event->id,
            'email' => $event->email,
        ]);
    }

    #[AsEventListener]
    public function onPostSave(SavePostEvent $event): void
    {
        $this->logger->info(sprintf('SavePost: id = %s', $event->post->getId()), [
            'post' => $event->post, // todo: {}
        ]);
    }

    #[AsEventListener]
    public function onPostDelete(DeletePostEvent $event): void
    {
        $this->logger->info('DeletePost: id = {id}', [
            'id' => $event->id,
        ]);
    }
}
