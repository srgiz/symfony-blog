<?php

declare(strict_types=1);

namespace Srgiz\KafkaTransport;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

readonly class KafkaSender implements SenderInterface
{
    public function __construct(
        private Connection $connection,
        private SerializerInterface $serializer,
    ) {
    }

    #[\Override]
    public function send(Envelope $envelope): Envelope
    {
        $messageStamp = $envelope->last(KafkaMessageStamp::class);

        if ($messageStamp) {
            throw new TransportException('No retries allowed.');
        }

        $keyStamp = $envelope->last(KafkaKeyStamp::class);
        $encodedMessage = $this->serializer->encode($envelope);

        try {
            $this->connection->producev(
                $encodedMessage['body'],
                $encodedMessage['headers'] ?? [],
                $keyStamp?->key,
            );
        } catch (\Throwable $e) {
            throw new TransportException($e->getMessage(), previous: $e);
        }

        return $envelope;
    }
}
