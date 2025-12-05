<?php

declare(strict_types=1);

namespace JanKlan\SimpleDateTimeForDoctrine;

use JanKlan\SimpleDateTime\SimpleTimeImmutable;

/**
 * Doctrine DBAL type for SimpleTimeImmutable value objects.
 */
final class SimpleTimeImmutableType extends AbstractSimpleTimeType
{
    public const string NAME = 'simple_time_immutable';

    #[\Override]
    protected function getTargetClass(): string
    {
        return SimpleTimeImmutable::class;
    }

    #[\Override]
    protected function createFromString(string $value): SimpleTimeImmutable
    {
        return SimpleTimeImmutable::fromString($value);
    }
}
