<?php

declare(strict_types=1);

namespace JanKlan\SimpleDateTimeForDoctrine;

use JanKlan\SimpleDateTime\SimpleTime;

/**
 * Doctrine DBAL type for mutable SimpleTime value objects.
 */
final class SimpleTimeType extends AbstractSimpleTimeType
{
    public const string NAME = 'simple_time';

    #[\Override]
    protected function getTargetClass(): string
    {
        return SimpleTime::class;
    }

    #[\Override]
    protected function createFromString(string $value): SimpleTime
    {
        return SimpleTime::fromString($value);
    }
}
