<?php

namespace SerginhoLD\JsonRpcBundle\Serializer;

interface SerializerInterface
{
    public function serialize(mixed $data): string;
}
