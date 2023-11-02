<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\Transport\Manticore;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

readonly class ManticoreTransport implements TransportInterface
{
    private array $configuration;

    public function __construct(
        private Connection $connection,
        private SerializerInterface $serializer,
        array $configuration,
    ) {
        $this->configuration = array_replace([
            'queue_name' => 'default'
        ], $configuration);
    }

    public function get(): iterable
    {
        $row = $this->connection->fetchAssociative(
            'select * from failed_message where failed_at = 0 order by created_at asc limit 1'
        );

        if (!$row) {
            return [];
        }

        $envelope = $this->serializer->decode([
            'body' => $row['body'],
            'headers' => json_decode($row['headers'], true),
        ]);

        return [$envelope->with(new TransportMessageIdStamp($row['id']))];
    }

    public function ack(Envelope $envelope): void
    {
        $stamp = $envelope->last(TransportMessageIdStamp::class);

        if (!$stamp instanceof TransportMessageIdStamp) {
            throw new TransportException('No TransportMessageIdStamp found on the Envelope.');
        }

        try {
            $this->connection->executeStatement('delete from failed_message where id = :id', ['id' => (int)$stamp->getId()], ['id' => Types::INTEGER]);
        } catch (DBALException $e) {
            throw new TransportException($e->getMessage(), 0, $e);
        }
    }

    public function reject(Envelope $envelope): void
    {
        $stamp = $envelope->last(TransportMessageIdStamp::class);

        if (!$stamp instanceof TransportMessageIdStamp) {
            throw new TransportException('No TransportMessageIdStamp found on the Envelope.');
        }

        try {
            $this->connection->executeStatement(
                'update failed_message set failed_at = :failed_at where id = :id',
                ['id' => (int)$stamp->getId(), 'failed_at' => time()],
                ['id' => Types::INTEGER, 'failed_at' => Types::INTEGER]
            );
        } catch (DBALException $e) {
            throw new TransportException($e->getMessage(), 0, $e);
        }
    }

    public function send(Envelope $envelope): Envelope
    {
        $encoded = $this->serializer->encode($envelope);

        try {
            $this->connection->executeStatement(
                'insert into failed_message (queue_name, message_class, body, headers, created_at, failed_at)
                values (:queue_name, :message_class, :body, :headers, :created_at, :failed_at)',
                [
                    'queue_name' => $this->configuration['queue_name'],
                    'message_class' => get_class($envelope->getMessage()),
                    'body' => $encoded['body'],
                    'headers' => json_encode($encoded['headers'] ?? [], JSON_UNESCAPED_UNICODE),
                    'created_at' => time(),
                    'failed_at' => 0,
                ]
            );

            $id = $this->connection->lastInsertId();
        } catch (DBALException $exception) {
            throw new TransportException($exception->getMessage(), 0, $exception);
        }

        return $envelope->with(new TransportMessageIdStamp($id));
    }
}
