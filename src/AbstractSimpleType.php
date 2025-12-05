<?php

declare(strict_types=1);

namespace JanKlan\SimpleDateTimeForDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Base class for all Simple Date/Time Doctrine types.
 *
 * Provides common logic for converting between PHP value objects and database values.
 */
abstract class AbstractSimpleType extends Type
{
    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?object
    {
        if (null === $value || '' === $value) {
            return null;
        }

        $targetClass = $this->getTargetClass();

        if ($value instanceof $targetClass) {
            return $value;
        }

        return $this->createFromString($value);
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format($this->getFormat());
        }

        return (string) $value;
    }

    public function getName(): string
    {
        if (!\defined('static::NAME')) {
            throw new \LogicException(\sprintf('Class %s doesn\'t define the NAME constant.', static::class));
        }

        return static::NAME;
    }

    /**
     * Returns the format string for database conversion (e.g., 'Y-m-d' or 'H:i:s').
     */
    abstract protected function getFormat(): string;

    /**
     * Returns the fully qualified class name of the target value object.
     *
     * @return class-string
     */
    abstract protected function getTargetClass(): string;

    /**
     * Creates a value object instance from a string.
     */
    abstract protected function createFromString(string $value): object;
}
