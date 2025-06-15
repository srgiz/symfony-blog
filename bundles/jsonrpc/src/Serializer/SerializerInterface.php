<?php

declare(strict_types=1);

namespace Srgiz\JsonRpcBundle\Serializer;

interface SerializerInterface
{
    public function serialize(mixed $data): string;
}
