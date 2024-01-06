<?php

declare(strict_types=1);

namespace SerginhoLD\JsonRpcBundle\Request;

use Symfony\Component\Validator\Constraints as Assert;

readonly class Payload
{
    public function __construct(
        #[Assert\EqualTo('2.0')]
        public ?string $jsonrpc,
        #[Assert\NotBlank]
        public ?string $method,
        public ?array $params = null,
        public int|string|null $id = null,
    ) {}

    public static function create(array $payload): self
    {
        return new self(
            $payload['jsonrpc'] ?? null,
            $payload['method'] ?? null,
            $payload['params'] ?? null,
            $payload['id'] ?? null,
        );
    }
}
