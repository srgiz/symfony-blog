<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Core\Entity\Message;
use App\Core\Serializer\SerializerInterface;
use Doctrine\DBAL\Connection;

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
}
