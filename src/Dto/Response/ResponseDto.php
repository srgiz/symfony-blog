<?php
declare(strict_types=1);

namespace App\Dto\Response;

use Symfony\Component\HttpFoundation\Cookie;

class ResponseDto implements ResponseDtoInterface, \JsonSerializable
{
    private ?array $meta = null;

    private mixed $data = null;

    private ?array $error = null;

    private array $cookies = [];

    public function getMeta(): ?array
    {
        return $this->meta;
    }

    public function setMetaParam(string $key, mixed $value): static
    {
        $this->meta[$key] = $value;
        return $this;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function setData(mixed $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function setError(int $code, string $message = null, array $data = null): static
    {
        $this->error['code'] = $code;
        $this->error['message'] = $message;

        if (null !== $data) {
            $this->error['data'] = $data;
        }

        return $this;
    }

    public function getCookies(): array
    {
        return $this->cookies;
    }

    public function setCookie(Cookie $cookie): static
    {
        $this->cookies[] = $cookie;
        return $this;
    }

    public function jsonSerialize(): ?array
    {
        $json = [];

        if (null !== $this->error) {
            $json['error'] = $this->error;
        } else {
            if (null !== $this->meta) {
                $json['meta'] = $this->meta;
                //ksort($json['meta'], SORT_NATURAL);
            }

            $json['data'] = $this->data;
        }

        return !empty($json) ? $json : null;
    }
}
