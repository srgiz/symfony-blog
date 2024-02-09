<?php

declare(strict_types=1);

namespace SerginhoLD\JsonRpcBundle\Request;

readonly class Payload
{
    public function __construct(
        public string $jsonrpc,
        public string $method,
        public ?array $params = null,
        public int|string|null $id = null,
    ) {}

    /**
     * @throws \TypeError
     */
    public static function create(array $payload): self
    {
        return new self(
            $payload['jsonrpc'],
            $payload['method'],
            $payload['params'] ?? null,
            $payload['id'] ?? null,
        );
    }
}
