<?php

declare(strict_types=1);

namespace App\Infrastructure\CommandBus;

use Doctrine\DBAL\Connection;

readonly class TransactionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private Connection $conn,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function __invoke(object $command, callable $next): ?object
    {
        return $this->conn->transactional(static function () use ($command, $next) {
            return $next($command);
        });
    }
}
