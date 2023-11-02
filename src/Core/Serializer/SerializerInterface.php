<?php

namespace App\Core\Serializer;

/**
 * @template T
 */
interface SerializerInterface
{
    /**
     * @psalm-param T $obj
     */
    public function normalize(object $obj): array;

    /**
     * @psalm-param class-string<T> $type
     * @psalm-return T
     */
    public function denormalize(array $data, string $type): object;
}
