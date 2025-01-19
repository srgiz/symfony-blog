<?php

declare(strict_types=1);

namespace App\Domain\Messenger;

interface MessageBusInterface
{
    public function send(object $object): void;
}
