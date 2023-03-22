<?php
declare(strict_types=1);

namespace App\Response;

readonly class JsonResponseDto
{
    public const ENCODING_OPTIONS = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR;

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
