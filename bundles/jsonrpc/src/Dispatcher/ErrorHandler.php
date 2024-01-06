<?php

declare(strict_types=1);

namespace SerginhoLD\JsonRpcBundle\Dispatcher;

use SerginhoLD\JsonRpcBundle\Exception\JsonRpcException;
use SerginhoLD\JsonRpcBundle\Response\JsonRpcResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ErrorHandler implements ErrorHandlerInterface
{
    public function handle(\Throwable|Response $error): JsonRpcResponse
    {
        if ($error instanceof Response) {
            return $this->handleResponse($error);
        }

        return $this->handleException($error);
    }

    private function handleException(\Throwable $exception): JsonRpcResponse
    {
        return match (true) {
            $exception instanceof JsonRpcException => JsonRpcResponse::fromError($exception->getCode(), $exception->getMessage()),
            $exception instanceof HttpExceptionInterface => JsonRpcResponse::fromError($exception->getStatusCode(), $exception->getMessage()),
            default => JsonRpcResponse::fromError(-32603, 'Internal error'),
        };
    }

    private function handleResponse(Response $response): JsonRpcResponse
    {
        if ($response->getStatusCode() >= 400 && isset(Response::$statusTexts[$response->getStatusCode()])) {
            return JsonRpcResponse::fromError($response->getStatusCode(), Response::$statusTexts[$response->getStatusCode()]);
        }

        return JsonRpcResponse::fromError(-32603, 'Internal error');
    }
}
