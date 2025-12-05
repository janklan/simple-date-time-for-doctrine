<?php

declare(strict_types=1);

namespace JanKlan\SimpleDateTimeForDoctrine;

use JanKlan\SimpleDateTime\SimpleDate;

/**
 * Doctrine DBAL type for mutable SimpleDate value objects.
 */
final class SimpleDateType extends AbstractSimpleDateType
{
    public const string NAME = 'simple_date';

    #[\Override]
    protected function getTargetClass(): string
    {
        return SimpleDate::class;
    }

    #[\Override]
    protected function createFromString(string $value): SimpleDate
    {
        return SimpleDate::fromString($value);
    }
}
