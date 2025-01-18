<?php

declare(strict_types=1);

namespace SerginhoLD\KafkaTransport;

use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class KafkaTransportFactory implements TransportFactoryInterface
{
    #[\Override]
    public function createTransport(#[\SensitiveParameter] string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        return new KafkaTransport(); // todo
    }

    #[\Override]
    public function supports(#[\SensitiveParameter] string $dsn, array $options): bool
    {
        return str_starts_with($dsn, 'kafka://');
    }
}
