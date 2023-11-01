<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger;

use App\Core\Messenger\CliOutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class CliOutput implements CliOutputInterface
{
    private ConsoleOutput $output;

    public function __construct()
    {
        $this->output = new ConsoleOutput();
    }

    public function writeln(string|iterable $messages): void
    {
        $this->output->writeln($messages);
    }
}
