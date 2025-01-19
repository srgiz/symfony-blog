<?php

declare(strict_types=1);

namespace SerginhoLD\KafkaTransport;

use Symfony\Component\Messenger\Stamp\StampInterface;

readonly class KafkaKeyStamp implements StampInterface
{
    public function __construct(
        public string $key,
    ) {
    }
}
