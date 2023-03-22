<?php
declare(strict_types=1);

namespace App\Response;

readonly class JsonResponseDto
{
    public function __construct(
        private object|array|null $data,
        private int $statusCode = 200
    ) {}

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getData(): object|array|null
    {
        return $this->data;
    }
}
