<?php

declare(strict_types=1);

namespace JanKlan\SimpleDateTimeForDoctrine;

use JanKlan\SimpleDateTime\SimpleDateImmutable;

/**
 * Doctrine DBAL type for SimpleDateImmutable value objects.
 */
final class SimpleDateImmutableType extends AbstractSimpleDateType
{
    public const string NAME = 'simple_date_immutable';

    #[\Override]
    protected function getTargetClass(): string
    {
        return SimpleDateImmutable::class;
    }

    #[\Override]
    protected function createFromString(string $value): SimpleDateImmutable
    {
        return SimpleDateImmutable::fromString($value);
    }
}
