<?php

declare(strict_types=1);

namespace App\Infrastructure\CommandBus;

interface MiddlewareInterface
{
    public function __invoke(object $command, callable $next): ?object;
}
