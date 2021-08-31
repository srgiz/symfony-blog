<?php
declare(strict_types=1);

namespace App\Doctrine\Validator\Constraints;

use App\Doctrine\Validator\UniqueEntityValidator;
use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class UniqueEntity extends Constraint
{
    public const NOT_UNIQUE_ERROR = '23bd9dbf-6b9b-41cd-a99e-4844bcf3077f';

    /** @var array<string, string> ['assertField' => 'entityField'] or ['entityField'] */
    public array $fields = [];

    public ?string $entityClass = null;

    public string $message = 'This value is already used.';

    public string $identifier = 'id';

    public function __construct(
        array $options = null,
        array $fields = null,
        string $entityClass = null,
        string $message = null,
        string $identifier = null,
        array $groups = null,
        $payload = null
    ) {
        if (null !== $fields) {
            $options['fields'] = $fields;
        }

        parent::__construct($options, $groups, $payload);

        $this->fields = $fields ?? $this->fields;
        $this->entityClass = $entityClass ?? $this->entityClass;
        $this->message = $message ?? $this->message;
        $this->identifier = $identifier ?? $this->identifier;
    }

    public function getRequiredOptions(): array
    {
        return ['fields'];
    }

    public function validatedBy(): string
    {
        return UniqueEntityValidator::class;
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
