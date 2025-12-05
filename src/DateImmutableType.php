<?php

declare(strict_types=1);

namespace JanKlan\SimpleDateTimeDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use JanKlan\SimpleDateTime\DateImmutable;

/**
 * Doctrine DBAL type for DateImmutable value objects.
 *
 * Maps to PostgreSQL/MySQL 'date' column type.
 */
class DateImmutableType extends Type
{
    public const string NAME = 'simple_date_immutable';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTypeDeclarationSQL($column);
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?DateImmutable
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if ($value instanceof DateImmutable) {
            return $value;
        }

        return DateImmutable::fromString($value);
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return (string) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
