<?php

declare(strict_types=1);

namespace App\Core\IndexRepository;

use App\Core\Index\FailedMessage;
use App\Core\Serializer\SerializerInterface;
use Doctrine\DBAL\Connection;

/**
 * @template TData of array{ uid: string, stream: string, body: string, created_at: int }
 */
readonly class FailedMessageRepository
{
    public function __construct(
        private Connection $manticoreConnection,
        private SerializerInterface $serializer,
    ) {}

    public function find(int $id): ?FailedMessage
    {
        /** @var TData|false $result */
        $result = $this->manticoreConnection->fetchAssociative('select * from failed_message where id = :id', ['id' => $id]);

        if (!$result) {
            return null;
        }

        return $this->prepare($result);
    }

    /** @psalm-suppress all */
    private function prepare(array $data): FailedMessage
    {
        $data['body'] = json_decode($data['body'], true);
        return $this->serializer->denormalize($data, FailedMessage::class);
    }

    public function save(FailedMessage $message): void
    {
        /** @var TData $data */
        $data = $this->serializer->normalize($message);
        $data['body'] = json_encode($data['body'], JSON_UNESCAPED_UNICODE);

        $this->manticoreConnection->executeStatement('insert into failed_message (uid, stream, body, created_at) values (:uid, :stream, :body, :created_at)', [
            'uid' => $data['uid'],
            'stream' => $data['stream'],
            'body' => $data['body'],
            'created_at' => $data['created_at'],
        ]);
    }
}
