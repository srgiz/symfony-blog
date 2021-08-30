<?php
declare(strict_types=1);

namespace App\Dto\Response;

class ResponseDto implements ResponseDtoInterface, \JsonSerializable
{
    private ?array $meta = null;

    private ?array $data = null;

    private ?array $error = null;

    public function setMetaPage(int $page, int $size, int $totalCount): static
    {
        $this->meta['page'] = $page;
        $this->meta['size'] = $size;
        $this->meta['total'] = $totalCount;
        $this->meta['totalPages'] = intval(ceil($totalCount / $size));

        return $this;
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

    public function jsonSerialize(): ?array
    {
        $json = [];

        if (null !== $this->error) {
            $json['error'] = $this->error;
        } else {
            if (null !== $this->meta) {
                $json['meta'] = $this->meta;
                ksort($json['meta'], SORT_NATURAL);
            }

            $json['data'] = $this->data;
        }

        return !empty($json) ? $json : null;
    }
}
