<?php

declare(strict_types=1);

namespace SerginhoLD\KafkaTransport;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

readonly class KafkaReceiver implements ReceiverInterface
{
    public function __construct(
        private Connection $connection,
        private SerializerInterface $serializer,
    ) {
    }

    /**
     * @return iterable<Envelope>
     */
    #[\Override]
    public function get(): iterable
    {
        $message = $this->connection->get();

        yield $this->serializer->decode([
            'body' => $message->payload,
            'headers' => $message->headers ?? [],
        ])->with(new KafkaMessageStamp($message))->with(new KafkaKeyStamp($message->key));
    }

    #[\Override]
    public function ack(Envelope $envelope): void
    {
        /** @var KafkaMessageStamp $messageStamp */
        $messageStamp = $envelope->last(KafkaMessageStamp::class);
        $this->connection->ack($messageStamp->message);
    }

    #[\Override]
    public function reject(Envelope $envelope): void
    {
        // stop consumer
        $errorDetailsStamp = $envelope->last(ErrorDetailsStamp::class);
        throw new TransportException($errorDetailsStamp->getExceptionMessage());
    }
}
