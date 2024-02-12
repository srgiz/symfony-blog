<?php

declare(strict_types=1);

namespace App\Infrastructure\Serializer;

use App\Core\Serializer\SerializerInterface;
use Symfony\Component\Serializer as SymfonySerializer;

/**
 * @template T
 * @template-implements SerializerInterface<T>
 */
readonly class Serializer implements SerializerInterface
{
    public function __construct(
        /** @var SymfonySerializer\Serializer */
        private SymfonySerializer\SerializerInterface $serializer,
    ) {
    }

    /** @psalm-suppress all */
    public function normalize(object $obj): array
    {
        return $this->serializer->normalize($obj);
    }

    /** @psalm-suppress all */
    public function denormalize(array $data, string $type): object
    {
        return $this->serializer->denormalize($data, $type);
    }
}
