<?php

declare(strict_types=1);

namespace SerginhoLD\JsonRpcBundle\Controller;

use SerginhoLD\JsonRpcBundle\Dispatcher\DispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
readonly class JsonRpcController
{
    public function __construct(
        private DispatcherInterface $handler,
    ) {}

    public function __invoke(Request $request): Response
    {
        return ($this->handler)($request);
    }
}
