<?php

declare(strict_types=1);

namespace SerginhoLD\JsonRpcBundle\Serializer;

interface SerializerInterface
{
    public function serialize(mixed $data): string;
}
