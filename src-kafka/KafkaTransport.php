<?php

declare(strict_types=1);

namespace SerginhoLD\KafkaTransport;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class KafkaTransport implements TransportInterface
{
    private Connection $connection;
    private SerializerInterface $serializer;

    public function __construct(
        ?Connection $connection = null, // todo
        ?SerializerInterface $serializer = null,
    ) {
        $this->connection = $connection ?? new Connection();
        $this->serializer = $serializer ?? new Serializer();
    }

    #[\Override]
    public function get(): iterable
    {
        throw new \RuntimeException('Not implemented');
    }

    #[\Override]
    public function ack(Envelope $envelope): void
    {
        throw new \RuntimeException('Not implemented');
    }

    #[\Override]
    public function reject(Envelope $envelope): void
    {
        throw new \RuntimeException('Not implemented');
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
}
