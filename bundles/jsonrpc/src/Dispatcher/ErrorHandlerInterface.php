<?php

namespace SerginhoLD\JsonRpcBundle\Dispatcher;

use SerginhoLD\JsonRpcBundle\Response\JsonRpcResponse;
use Symfony\Component\HttpFoundation\Response;

interface ErrorHandlerInterface
{
    public function handle(\Throwable|Response $error): JsonRpcResponse;
}
