<?php

declare(strict_types=1);

namespace SerginhoLD\KafkaTransport;

use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;

readonly class KafkaKeyStamp implements NonSendableStampInterface
{
    public function __construct(
        public ?string $key,
    ) {
    }
}
