<?php

declare(strict_types=1);

namespace Srgiz\KafkaTransport;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

readonly class KafkaTransportFactory implements TransportFactoryInterface
{
    public function __construct(
        private ?LoggerInterface $logger = null,
    ) {
    }

    #[\Override]
    public function createTransport(#[\SensitiveParameter] string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        $connection = Connection::fromDsn($dsn, $options);

        if ($this->logger) {
            $connection->setLogger($this->logger);
        }

        return new KafkaTransport($connection, $serializer);
    }

    #[\Override]
    public function supports(#[\SensitiveParameter] string $dsn, array $options): bool
    {
        return str_starts_with($dsn, 'kafka://');
    }
}
