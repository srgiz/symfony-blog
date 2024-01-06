<?php

declare(strict_types=1);

namespace SerginhoLD\JsonRpcBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class JsonRpcResponse extends JsonResponse implements \JsonSerializable
{
    private int|string|null $id = null;

    public function __construct(
        readonly public mixed $result,
        readonly public ?JsonRpcError $error = null,
        array $headers = [],
    ) {
        $headers['Content-Type'] = 'application/json';
        parent::__construct(data: null, headers: $headers, json: false);
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function setId(int|string|null $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'jsonrpc' => '2.0',
            'result' => $this->result,
            'error' => $this->error,
            'id' => $this->id,
        ], fn($value, $key) => null !== $value || $key === 'id', ARRAY_FILTER_USE_BOTH);
    }

    final public function setJson(string $json): static
    {
        throw new \RuntimeException('Method not supported');
    }

    final public function setData(mixed $data = []): static
    {
        // skip parent logic
        return $this;
    }

    final public function getContent(): string
    {
        $this->data = json_encode($this->jsonSerialize(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
        $this->update();
        return $this->content;
    }

    final public function sendContent(): static
    {
        echo $this->getContent();
        return $this;
    }

    public static function fromError(int $code, string $message): self
    {
        return new self(null, new JsonRpcError($code, $message));
    }
}
