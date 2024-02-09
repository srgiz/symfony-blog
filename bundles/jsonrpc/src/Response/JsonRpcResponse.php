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

    /**
     * @throws \LogicException
     */
    public function __construct(
        readonly public mixed $result,
        readonly public ?JsonRpcError $error = null,
        array $headers = [],
    ) {
        if (
            (null !== $result && null !== $this->error)
            || (null === $result && null === $this->error)
        ) {
            throw new \LogicException('Only the result member or only the error member must be included');
        }

        $headers['Content-Type'] = 'application/json';
        parent::__construct(data: null, headers: $headers, json: false);
    }

    public static function fromError(int $code, string $message, ?object $data = null): self
    {
        return new self(null, new JsonRpcError($code, $message, $data));
    }

    /**
     * @internal
     */
    final public function setId(int|string|null $id): static
    {
        $this->id = $id;
        return $this;
    }

    final public function jsonSerialize(): array
    {
        return array_filter([
            'jsonrpc' => '2.0',
            'result' => $this->result,
            'error' => $this->error,
            'id' => $this->id,
        ], fn($value, $key) => null !== $value || $key === 'id', ARRAY_FILTER_USE_BOTH);
    }

    /**
     * @throws \LogicException
     */
    final public function setJson(string $json): static
    {
        throw new \LogicException('Method not supported');
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
}
