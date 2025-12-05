<?php

declare(strict_types=1);

namespace JanKlan\SimpleDateTimeDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use JanKlan\SimpleDateTime\Time;

/**
 * Doctrine DBAL type for mutable Time value objects.
 *
 * Maps to PostgreSQL 'time' or MySQL 'time' column type.
 */
class TimeType extends Type
{
    public const string NAME = 'simple_time';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getTimeTypeDeclarationSQL($column);
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Time
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if ($value instanceof Time) {
            return $value;
        }

        return Time::fromString($value);
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
