<?php
declare(strict_types=1);

namespace App\Doctrine\Types;

class TimestampImmutableMs extends DateTimeImmutableMs
{
    protected string $sqlDeclaration = 'TIMESTAMP';

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'timestamp_immutable_ms';
    }
}
