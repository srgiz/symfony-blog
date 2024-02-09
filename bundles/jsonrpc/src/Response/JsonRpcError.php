<?php

declare(strict_types=1);

namespace SerginhoLD\JsonRpcBundle\Response;

readonly class JsonRpcError implements \JsonSerializable
{
    public function __construct(
        public int $code,
        public string $message,
        public ?object $data = null,
    ) {}

    public function jsonSerialize(): array
    {
        return array_filter([
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data,
        ], fn($value) => null !== $value);
    }
}
