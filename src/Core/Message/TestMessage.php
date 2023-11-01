<?php

declare(strict_types=1);

namespace App\Core\Message;

readonly class TestMessage
{
    public function __construct(
        public int $time,
    ) {}
}
