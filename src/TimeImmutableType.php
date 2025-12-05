<?php

declare(strict_types=1);

namespace JanKlan\SimpleDateTimeDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use JanKlan\SimpleDateTime\TimeImmutable;

/**
 * Doctrine DBAL type for TimeImmutable value objects.
 *
 * Maps to PostgreSQL 'time' or MySQL 'time' column type.
 */
class TimeImmutableType extends Type
{
    public const string NAME = 'simple_time_immutable';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getTimeTypeDeclarationSQL($column);
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?TimeImmutable
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if ($value instanceof TimeImmutable) {
            return $value;
        }

        return TimeImmutable::fromString($value);
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('H:i:s');
        }

        return (string) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
