<?php

declare(strict_types=1);

namespace App\Core\Index;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class FailedMessage
{
    public ?int $id = null;

    public ?string $uid = null;

    public ?string $stream = null;

    // todo: context json
    public ?array $body = null;

    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'U'], denormalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'U'])]
    public ?\DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
