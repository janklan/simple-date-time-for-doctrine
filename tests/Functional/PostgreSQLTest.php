<?php

declare(strict_types=1);

namespace JanKlan\SimpleDateTimeForDoctrine\Tests\Functional;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use JanKlan\SimpleDateTimeForDoctrine\SimpleDateImmutableType;
use JanKlan\SimpleDateTimeForDoctrine\SimpleDateType;
use JanKlan\SimpleDateTimeForDoctrine\SimpleTimeImmutableType;
use JanKlan\SimpleDateTimeForDoctrine\SimpleTimeType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;

/**
 * Functional tests for PostgreSQL database integration.
 *
 * @internal
 */
#[CoversClass(SimpleDateType::class)]
#[CoversClass(SimpleDateImmutableType::class)]
#[CoversClass(SimpleTimeType::class)]
#[CoversClass(SimpleTimeImmutableType::class)]
#[Group('functional')]
#[Group('postgres')]
#[RequiresPhpExtension('pdo_pgsql')]
class PostgreSQLTest extends AbstractDatabaseTestCase
{
    protected function createConnection(): Connection
    {
        return DriverManager::getConnection([
            'driver' => 'pdo_pgsql',
            'host' => getenv('POSTGRES_HOST') ?: '127.0.0.1',
            'port' => (int) (getenv('POSTGRES_PORT') ?: '15432'),
            'dbname' => getenv('POSTGRES_DB') ?: 'test',
            'user' => getenv('POSTGRES_USER') ?: 'test',
            'password' => getenv('POSTGRES_PASSWORD') ?: 'test',
        ]);
    }

    protected function getDateColumnType(): string
    {
        return 'DATE';
    }

    protected function getTimeColumnType(): string
    {
        return 'TIME WITHOUT TIME ZONE';
    }
}
