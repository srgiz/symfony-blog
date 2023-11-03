<?php

declare(strict_types=1);

namespace App\Symfony\Messenger\Transport;

use App\Symfony\Messenger\Transport\Manticore\ManticoreTransport;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Exception\InvalidArgumentException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

readonly class ManticoreTransportFactory implements TransportFactoryInterface
{
    public function __construct(
        private ManagerRegistry $registry,
    ) {}

    public function createTransport(#[\SensitiveParameter] string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        $url = parse_url($dsn);

        if (!isset($url['host'])) {
            throw new InvalidArgumentException('The given Manticore Messenger DSN is invalid.');
        }

        $configuration = ['connection' => $url['host']];

        if (isset($url['query'])) {
            $query = [];
            parse_str($url['query'], $query);
            $configuration += $query;
        }

        /** @var Connection $conn */
        $conn = $this->registry->getConnection($url['host']);
        return new ManticoreTransport($conn, $serializer, $configuration);
    }

    public function supports(#[\SensitiveParameter] string $dsn, array $options): bool
    {
        return str_starts_with($dsn, 'manticore://');
    }
}
