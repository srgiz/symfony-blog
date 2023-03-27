<?php
declare(strict_types=1);

namespace App\Core\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

class StringArrayType extends Type
{
    public function getName(): string
    {
        return 'string_array';
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $length = intval($column['length'] ?? 255);
        return "VARCHAR({$length})[]";
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!is_array($value)) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                $this->getName(),
                ['null', 'array']
            );
        }

        /** @psalm-var string[]|null $value */
        $sqlValue = '';

        if (!empty($value)) {
            foreach ($value as &$str) {
                $escape = false;

                if (false !== mb_strrpos($str, "\\")) {
                    $str = str_replace("\\", "\\\\", $str); // sql escape
                    $escape = true;
                }

                if (false !== mb_strrpos($str, '"')) {
                    $str = str_replace('"', "\\\"", $str); // sql escape
                    $escape = true;
                }

                if (
                    !$escape
                    && (
                        false !== mb_strrpos($str, ',')
                        || false !== mb_strrpos($str, '{')
                        || false !== mb_strrpos($str, '}')
                        || false !== mb_strrpos($str, "\n")
                    )
                ) {
                    $escape = true;
                }

                if ($escape) {
                    $str = '"' . $str . '"';
                }
            }

            unset($str);
            $sqlValue = implode(',', $value);
        }

        return '{' . $sqlValue . '}';
    }

    /**
     * @return array<int, mixed>|null
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?array
    {
        /** @psalm-var string|null $value */
        if (null === $value) {
            return null;
        }

        $csv = mb_substr($value, 1, mb_strlen($value) - 2); // {}

        if ('' === $csv) {
            return [];
        }

        $csv = str_replace("\\\"", '""', $csv); // csv escape
        $csv = str_replace("\\\\", "\\", $csv); // sql escape

        return str_getcsv($csv, ',', '"', '"');
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
