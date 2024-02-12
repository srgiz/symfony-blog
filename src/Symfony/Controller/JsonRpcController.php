<?php

declare(strict_types=1);

namespace App\Symfony\Controller;

use SerginhoLD\JsonRpcBundle\Controller\JsonRpcController as Controller;
use SerginhoLD\JsonRpcBundle\Exception\JsonRpcResponseException;
use SerginhoLD\JsonRpcBundle\Response\JsonRpcResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[AsController]
#[Route('/jsonrpc', 'jsonrpc', methods: ['GET', 'POST'])]
readonly class JsonRpcController extends Controller
{
    protected function createExceptionResponse(\Throwable $exception): JsonRpcResponse
    {
        switch (true) {
            case $exception instanceof JsonRpcResponseException:
                $code = $exception->getResponse()->getStatusCode();
                [$code, $message] = $code >= 400 && isset(Response::$statusTexts[$code]) ? [$code, Response::$statusTexts[$code]] : [-32603, 'Internal error'];
                return JsonRpcResponse::fromError($code, $message);

            case $exception instanceof HttpExceptionInterface:
                return JsonRpcResponse::fromError($exception->getStatusCode(), $exception->getMessage());

            case $exception instanceof AccessDeniedException:
                return JsonRpcResponse::fromError(401, 'Unauthorized');

            default:
                return JsonRpcResponse::fromError(-32603, 'Internal error');
        }
    }
}