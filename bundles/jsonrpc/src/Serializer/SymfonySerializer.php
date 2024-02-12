<?php

declare(strict_types=1);

namespace SerginhoLD\JsonRpcBundle\Serializer;

use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

readonly class SymfonySerializer implements SerializerInterface
{
    public function __construct(
        private SymfonySerializerInterface $serializer,
    ) {
    }

    public function serialize(mixed $data): string
    {
        return $this->serializer->serialize($data, 'json');
    }
}
