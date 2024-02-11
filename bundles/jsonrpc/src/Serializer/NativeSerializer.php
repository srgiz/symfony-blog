<?php

declare(strict_types=1);

namespace SerginhoLD\JsonRpcBundle\Serializer;

use Symfony\Component\HttpFoundation\JsonResponse;

class NativeSerializer implements SerializerInterface
{
    public function serialize(mixed $data): string
    {
        return json_encode($data, JsonResponse::DEFAULT_ENCODING_OPTIONS | JSON_UNESCAPED_UNICODE);
    }
}