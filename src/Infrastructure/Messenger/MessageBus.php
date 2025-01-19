<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger;

use App\Domain\Blog\Message\TestMessage;
use App\Domain\Messenger\MessageBusInterface;
use SerginhoLD\KafkaTransport\KafkaKeyStamp;
use Symfony\Component\Messenger\MessageBusInterface as SymfonyMessageBusInterface;

readonly class MessageBus implements MessageBusInterface
{
    public function __construct(
        private SymfonyMessageBusInterface $bus,
    ) {
    }

    public function send(object $object): void
    {
        $this->bus->dispatch($object, $this->createStamps($object));
    }

    private function createStamps(object $object): array
    {
        $stamps = [];

        switch (true) {
            case $object instanceof TestMessage:
                $stamps[] = new KafkaKeyStamp('key.'.$object->testValue);
                break;
        }

        return $stamps;
    }
}
