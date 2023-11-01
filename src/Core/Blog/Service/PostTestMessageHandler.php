<?php

declare(strict_types=1);

namespace App\Core\Blog\Service;

use App\Core\Message\TestMessage;
use App\Core\Messenger\CliOutputInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class PostTestMessageHandler
{
    public function __construct(
        private CliOutputInterface $output,
    ) {}

    public function __invoke(TestMessage $message): void
    {
        $this->output->writeln((string) $message->time);
    }
}
