<?php

declare(strict_types=1);

namespace SerginhoLD\KafkaTransport;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class KafkaTransport implements TransportInterface
{
    private ?KafkaSender $sender = null;
    private ?KafkaReceiver $receiver = null;

    public function __construct(
        private readonly Connection $connection,
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[\Override]
    public function get(): iterable
    {
        return $this->getReceiver()->get();
    }

    #[\Override]
    public function ack(Envelope $envelope): void
    {
        $this->getReceiver()->ack($envelope);
    }

    #[\Override]
    public function reject(Envelope $envelope): void
    {
        $this->getReceiver()->reject($envelope);
    }

    #[\Override]
    public function send(Envelope $envelope): Envelope
    {
        return $this->getSender()->send($envelope);
    }

    private function getSender(): KafkaSender
    {
        return $this->sender ??= new KafkaSender($this->connection, $this->serializer);
    }

    private function getReceiver(): KafkaReceiver
    {
        return $this->receiver ??= new KafkaReceiver($this->connection, $this->serializer);
    }
}
