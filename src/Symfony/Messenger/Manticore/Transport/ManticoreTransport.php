<?php

declare(strict_types=1);

namespace App\Symfony\Messenger\Manticore\Transport;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * @template TRow of array{id: mixed, body: string, headers: string}
 */
class ManticoreTransport implements TransportInterface, ListableReceiverInterface
{
    public const DEFAULT_TABLE = 'messenger_default';

    /** @var array{table_name: string, queue_name: string, delete_after_ack: bool, delete_after_reject: bool} */
    private array $configuration;

    private bool $tableIsCached = false;

    public function __construct(
        private Connection $connection,
        private SerializerInterface $serializer,
        private CacheItemPoolInterface $cache,
        array $configuration,
    ) {
        /** @psalm-suppress InvalidPropertyAssignmentValue */
        $this->configuration = array_replace([
            'table_name' => self::DEFAULT_TABLE,
            'queue_name' => 'default',
            'delete_after_ack' => true,
            'delete_after_reject' => true,
        ], $configuration);
    }

    public function get(): iterable
    {
        $this->setup();

        /** @var TRow|false $row */
        $row = $this->connection->fetchAssociative(
            "select * from {$this->configuration['table_name']} where queue_name = :queue_name and delivered_at = 0 order by created_at asc, id asc limit 1",
            ['queue_name' => $this->configuration['queue_name']]
        );

        return $row ? [$this->decodeEnvelope($row)] : [];
    }

    /**
     * @psalm-param TRow $row
     */
    private function decodeEnvelope(array $row): Envelope
    {
        $envelope = $this->serializer->decode([
            'body' => $row['body'],
            'headers' => json_decode($row['headers'], true),
        ]);

        return $envelope->with(new TransportMessageIdStamp($row['id']));
    }

    public function ack(Envelope $envelope): void
    {
        $stamp = $envelope->last(TransportMessageIdStamp::class);

        if (!$stamp instanceof TransportMessageIdStamp) {
            throw new TransportException('No TransportMessageIdStamp found on the Envelope.');
        }

        try {
            $this->setup();

            if (filter_var($this->configuration['delete_after_ack'], FILTER_VALIDATE_BOOL)) {
                $this->connection->executeStatement("delete from {$this->configuration['table_name']} where id = :id", ['id' => (int) $stamp->getId()], ['id' => Types::INTEGER]);
                return;
            }

            $this->connection->executeStatement(
                "update {$this->configuration['table_name']} set delivered_at = :delivered_at where id = :id",
                [
                    'delivered_at' => time(),
                    'id' => (int) $stamp->getId(),
                ],
                [
                    'delivered_at' => Types::INTEGER,
                    'id' => Types::INTEGER,
                ]
            );
        } catch (\Exception $e) {
            throw new TransportException($e->getMessage(), 0, $e);
        }
    }

    public function reject(Envelope $envelope): void
    {
        $stamp = $envelope->last(TransportMessageIdStamp::class);

        if (!$stamp instanceof TransportMessageIdStamp) {
            throw new TransportException('No TransportMessageIdStamp found on the Envelope.');
        }

        $params = $this->envelopeToParams($envelope);

        try {
            $this->setup();

            if (filter_var($this->configuration['delete_after_reject'], FILTER_VALIDATE_BOOL)) {
                $this->connection->executeStatement("delete from {$this->configuration['table_name']} where id = :id", ['id' => (int) $stamp->getId()], ['id' => Types::INTEGER]);
                return;
            }

            $this->connection->executeStatement(
                "update {$this->configuration['table_name']} set body = :body, headers = :headers, failed_at = :failed_at where id = :id",
                [
                    'body' => $params['body'],
                    'headers' => $params['headers'],
                    'failed_at' => $params['failed_at'],
                    'delivered_at' => time(),
                    'id' => (int) $stamp->getId(),
                ],
                [
                    'failed_at' => Types::INTEGER,
                    'delivered_at' => Types::INTEGER,
                    'id' => Types::INTEGER,
                ]
            );
        } catch (\Exception $e) {
            throw new TransportException($e->getMessage(), 0, $e);
        }
    }

    public function send(Envelope $envelope): Envelope
    {
        try {
            $this->setup();

            $this->connection->executeStatement(
                "insert into {$this->configuration['table_name']} (queue_name, message_class, body, headers, created_at, failed_at, delivered_at)
                values (:queue_name, :message_class, :body, :headers, :created_at, :failed_at, :delivered_at)",
                $this->envelopeToParams($envelope)
            );

            $id = $this->connection->lastInsertId();
        } catch (\Exception $exception) {
            throw new TransportException($exception->getMessage(), 0, $exception);
        }

        return $envelope->with(new TransportMessageIdStamp($id));
    }

    private function envelopeToParams(Envelope $envelope): array
    {
        $encoded = $this->serializer->encode($envelope);
        $redeliveryStamp = $envelope->last(RedeliveryStamp::class);

        return [
            'queue_name' => $this->configuration['queue_name'],
            'message_class' => get_class($envelope->getMessage()),
            'body' => $encoded['body'],
            'headers' => json_encode($encoded['headers'] ?? [], JSON_UNESCAPED_UNICODE),
            'created_at' => time(),
            'failed_at' => $redeliveryStamp ? $redeliveryStamp->getRedeliveredAt()->getTimestamp() : 0,
            'delivered_at' => 0,
        ];
    }

    public function all(?int $limit = null): iterable
    {
        $this->setup();

        $sql = "select * from {$this->configuration['table_name']} where queue_name = :queue_name and delivered_at = 0 order by created_at asc, id asc";

        if ($limit) {
            $sql .= ' limit '.$limit;
        }

        $rows = $this->connection->fetchAllAssociative($sql, ['queue_name' => $this->configuration['queue_name']]);

        /** @psalm-suppress ArgumentTypeCoercion */
        return array_map(fn ($row) => $this->decodeEnvelope($row), $rows);
    }

    public function find(mixed $id): ?Envelope
    {
        $this->setup();

        /** @var TRow|false $row */
        $row = $this->connection->fetchAssociative(
            "select * from {$this->configuration['table_name']} where id = :id",
            ['id' => $id],
            ['id' => Types::INTEGER]
        );

        return $row ? $this->decodeEnvelope($row) : null;
    }

    private function setup(): void
    {
        if ($this->tableIsCached) {
            return;
        }

        $this->tableIsCached = true;
        $name = $this->configuration['table_name'];
        $cache = $this->cache->getItem('messenger_manticore_'.$name);

        if ($cache->isHit()) {
            return;
        }

        /** @var string|false $foundName */
        $foundName = $this->connection->fetchOne('show tables like :name', ['name' => $name]);

        if (!$foundName) {
            $this->connection->executeStatement("
            CREATE TABLE {$name}(
                id bigint, queue_name string, message_class string, body text attribute, headers json,
                created_at timestamp, failed_at timestamp, delivered_at timestamp
            )
            ");
        }

        $this->cache->save($cache->set(true));
    }
}
