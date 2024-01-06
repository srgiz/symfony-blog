<?php

namespace SerginhoLD\JsonRpcBundle\Dispatcher;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface DispatcherInterface
{
    public function __invoke(Request $request): JsonResponse;
}
