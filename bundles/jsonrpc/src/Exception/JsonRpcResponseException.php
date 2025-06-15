<?php

declare(strict_types=1);

namespace Srgiz\JsonRpcBundle\Exception;

use Symfony\Component\HttpFoundation\Response;

class JsonRpcResponseException extends \Exception
{
    private Response $response;

    public function __construct(Response $response, ?\Throwable $previous = null)
    {
        parent::__construct(code: $response->getStatusCode(), previous: $previous);
        $this->response = $response;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
