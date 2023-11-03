<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Core\Entity\Message;
use App\Core\Serializer\SerializerInterface;
use Doctrine\DBAL\Connection;

/**
 * @template TMessages of array<Message>
 * @template TPaginator of array{count: int, list: TMessages}
 */
readonly class MessageRepository
{
    public function __construct(
        private Connection $manticoreConnection,
        private SerializerInterface $serializer,
    ) {}

    /** @psalm-suppress MixedInferredReturnType */
    public function find(string $table, int $id): ?Message
    {
        $data = $this->manticoreConnection->fetchAssociative("select * from {$table} where id = :id", ['id' => $id]);

        if (!$data) {
            return null;
        }

        /** @psalm-suppress all */
        return $this->serializer->denormalize($data, Message::class);
    }

    /**
     * @psalm-return TPaginator
     */
    public function paginate(string $table, int $offset, int $limit): array
    {
        $paginator = ['list' => []];
        $sql = "select %s from {$table}";

        $paginator['count'] = (int)$this->manticoreConnection->fetchOne(sprintf($sql, 'count(*)'));

        /** @var TPaginator $paginator */
        if (!$paginator['count']) {
            return $paginator;
        }

        $list = $this->manticoreConnection->fetchAllAssociative(sprintf($sql . ' order by created_at asc, id asc limit %s, %s', '*', $offset, $limit));

        foreach ($list as $data) {
            /** @psalm-suppress all */
            $paginator['list'][] = $this->serializer->denormalize($data, Message::class);
        }

        return $paginator;
    }
}
