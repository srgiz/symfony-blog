<?php

declare(strict_types=1);

namespace App\Core\Entity;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

readonly class Message
{
    public function __construct(
        public int $id,
        public string $queueName,
        public string $messageClass,
        #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'U'], denormalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'U'])]
        public \DateTimeInterface $createdAt,
    ) {
    }
}
