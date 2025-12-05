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
 * Functional tests for MySQL database integration.
 *
 * @internal
 */
#[CoversClass(SimpleDateType::class)]
#[CoversClass(SimpleDateImmutableType::class)]
#[CoversClass(SimpleTimeType::class)]
#[CoversClass(SimpleTimeImmutableType::class)]
#[Group('functional')]
#[Group('mysql')]
#[RequiresPhpExtension('pdo_mysql')]
class MySQLTest extends AbstractDatabaseTestCase
{
    protected function createConnection(): Connection
    {
        return DriverManager::getConnection([
            'driver' => 'pdo_mysql',
            'host' => getenv('MYSQL_HOST') ?: '127.0.0.1',
            'port' => (int) (getenv('MYSQL_PORT') ?: '13306'),
            'dbname' => getenv('MYSQL_DB') ?: 'test',
            'user' => getenv('MYSQL_USER') ?: 'test',
            'password' => getenv('MYSQL_PASSWORD') ?: 'test',
            'charset' => 'utf8mb4',
        ]);
    }

    protected function getDateColumnType(): string
    {
        return 'DATE';
    }

    protected function getTimeColumnType(): string
    {
        return 'TIME';
    }
}
