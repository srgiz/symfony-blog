<?php

namespace App\Core\Messenger;

interface CliOutputInterface
{
    public function writeln(string|iterable $messages): void;
}
