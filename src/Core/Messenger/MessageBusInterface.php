<?php

namespace App\Core\Messenger;

interface MessageBusInterface
{
    public function dispatch(object $obj): void;
}
