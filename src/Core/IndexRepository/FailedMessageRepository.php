<?php

declare(strict_types=1);

namespace App\Core\IndexRepository;

use App\Core\Index\FailedMessage;
use Doctrine\DBAL\Connection;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

readonly class FailedMessageRepository
{
    public function __construct(
        private Connection $manticoreConnection,
        /** @var Serializer */
        private SerializerInterface $serializer,// todo: infrastructure
    ) {}

    public function find(int $id): ?FailedMessage
    {
        $result = $this->manticoreConnection->fetchAssociative('select * from failed_message where id = :id', ['id' => $id]);

        if (!$result) {
            return null;
        }

        return $this->prepare($result);
    }

    private function prepare(array $data): FailedMessage
    {
        $data['body'] = json_decode($data['body'], true);
        return $this->serializer->denormalize($data, FailedMessage::class);
    }

    public function save(FailedMessage $message): void
    {
        /** @var array $data */
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
