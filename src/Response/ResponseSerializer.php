<?php
declare(strict_types=1);

namespace App\Response;

use App\Dto\Response\ResponseDtoInterface;
use App\Response\Format\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class ResponseSerializer implements ResponseSerializerInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function serialize(ResponseDtoInterface $data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        $json = $this->serializer->serialize($data, 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], $context));

        return new JsonResponse($json, $status, $headers, true);
    }
}
