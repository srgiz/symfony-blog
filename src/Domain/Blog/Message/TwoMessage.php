<?php

declare(strict_types=1);

namespace App\Domain\Blog\Message;

//test kafka
readonly class TwoMessage
{
    public function __construct(
        public string $foo,
        public string $type = 'foo',
    ) {
    }
}
