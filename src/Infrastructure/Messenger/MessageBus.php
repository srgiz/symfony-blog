<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger;

use App\Core\Messenger\MessageBusInterface;
use Symfony\Component\Messenger as SymfonyMessenger;

readonly class MessageBus implements MessageBusInterface
{
    public function __construct(
        private SymfonyMessenger\MessageBusInterface $bus,
    ) {
    }

    public function dispatch(object $obj): void
    {
        $this->bus->dispatch($obj);
    }
}
