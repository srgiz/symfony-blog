<?php
declare(strict_types=1);

namespace App\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\PhpIntegerMappingType;
use Doctrine\DBAL\Types\Type;

/**
 * todo: PhpIntegerMappingType костыль для DEFAULT CURRENT_TIMESTAMP(6)
 * @see \Doctrine\DBAL\Platforms\MySqlPlatform::getAlterTableSQL
 * @see \Doctrine\DBAL\Platforms\AbstractPlatform::getColumnDeclarationSQL
 * @see \Doctrine\DBAL\Platforms\AbstractPlatform::getDefaultValueDeclarationSQL
 */
class DateTimeImmutableMs extends Type implements PhpIntegerMappingType
{
    private const FORMAT = 'Y-m-d H:i:s.u';

    protected string $sqlDeclaration = 'DATETIME';

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'datetime_immutable_ms';
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $precision = $column['precision'] > 6 ? 6 : $column['precision'];
        return "{$this->sqlDeclaration}({$precision})";
    }

    /**
     * {@inheritdoc}
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null)
            return null;

        if ($value instanceof \DateTimeImmutable)
            return $value->format(self::FORMAT);

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            ['null', \DateTimeImmutable::class]
        );
    }

    /**
     * {@inheritdoc}
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof \DateTimeImmutable)
            return $value;

        $dateTime = \DateTimeImmutable::createFromFormat(self::FORMAT, $value);

        if (!$dateTime) {
            $dateTime = date_create_immutable($value);
        }

        if ($dateTime)
            return $dateTime;

        throw ConversionException::conversionFailedFormat(
            $value,
            $this->getName(),
            self::FORMAT
        );
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
