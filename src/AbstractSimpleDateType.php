<?php

declare(strict_types=1);

namespace JanKlan\SimpleDateTimeForDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Base class for Simple Date Doctrine types.
 *
 * Maps to PostgreSQL/MySQL 'date' column type.
 */
abstract class AbstractSimpleDateType extends AbstractSimpleType
{
    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTypeDeclarationSQL($column);
    }

    #[\Override]
    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        return ['date'];
    }

    #[\Override]
    protected function getFormat(): string
    {
        return 'Y-m-d';
    }
}
