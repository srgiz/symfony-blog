<?php

declare(strict_types=1);

namespace Srgiz\JsonRpcBundle\Request;

readonly class Payload
{
    public function __construct(
        public string $jsonrpc,
        public string $method,
        public ?array $params = null,
        public int|string|null $id = null,
    ) {
    }

    /**
     * @throws \TypeError
     *
     * @psalm-suppress MixedArgument
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
