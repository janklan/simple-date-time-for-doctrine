<?php

declare(strict_types=1);

namespace JanKlan\SimpleDateTimeForDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Base class for Simple Time Doctrine types.
 *
 * Maps to PostgreSQL 'time' or MySQL 'time' column type.
 */
abstract class AbstractSimpleTimeType extends AbstractSimpleType
{
    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getTimeTypeDeclarationSQL($column);
    }

    #[\Override]
    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        return ['time'];
    }

    #[\Override]
    protected function getFormat(): string
    {
        return 'H:i:s';
    }
}
