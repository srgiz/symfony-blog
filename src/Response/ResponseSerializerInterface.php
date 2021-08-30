<?php
namespace App\Response;

use App\Dto\Response\ResponseDtoInterface;
use App\Response\Format\JsonResponse;

interface ResponseSerializerInterface
{
    public function serialize(ResponseDtoInterface $data, int $status = 200, array $headers = [], array $context = []): JsonResponse;
}
