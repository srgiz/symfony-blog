<?php

declare(strict_types=1);

namespace SerginhoLD\JsonRpcBundle\Exception;

class JsonRpcException extends \Exception
{
    private ?object $data;

    public function __construct(string $message = '', int $code = 0, ?object $data = null, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->data = $data;
    }

    public function getData(): ?object
    {
        return $this->data;
    }
}
