<?php

declare(strict_types=1);

namespace App\Symfony\Consumer;

use App\Domain\Blog\Message\TestMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class TestMessageConsumer
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(TestMessage $message)
    {
        //throw new \Exception('Oops');
        $this->logger->info('Consume "TestMessage"', ['type' => $message->type, 'value' => $message->testValue]);
    }
}
