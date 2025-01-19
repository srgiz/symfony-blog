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

        switch ($message->err) {
            case RD_KAFKA_RESP_ERR__PARTITION_EOF: // No more messages
            case RD_KAFKA_RESP_ERR__TIMED_OUT: // Attempt to connect again
                return;

            case RD_KAFKA_RESP_ERR_NO_ERROR:
                break;

            default:
                throw new TransportException($message->errstr(), $message->err);
        }

        yield $this->serializer->decode([
            'body' => $message->payload,
            'headers' => $message->headers ?? [],
        ])->with(new KafkaMessageStamp($message));
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
