<?php

declare(strict_types=1);

namespace App\Symfony\Consumer;

use App\Domain\Blog\Message\TwoMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TwoMessageConsumer
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(TwoMessage $message)
    {
        //throw new \Exception('Oops');
        $this->logger->info('Consume "TwoMessage"', ['type' => $message->type, 'foo' => $message->foo]);
    }
}
