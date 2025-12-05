<?php

declare(strict_types=1);

namespace JanKlan\SimpleDateTimeForDoctrine\Tests\Functional;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use JanKlan\SimpleDateTime\SimpleDate;
use JanKlan\SimpleDateTime\SimpleDateImmutable;
use JanKlan\SimpleDateTime\SimpleTime;
use JanKlan\SimpleDateTime\SimpleTimeImmutable;
use JanKlan\SimpleDateTimeForDoctrine\SimpleDateImmutableType;
use JanKlan\SimpleDateTimeForDoctrine\SimpleDateType;
use JanKlan\SimpleDateTimeForDoctrine\SimpleTimeImmutableType;
use JanKlan\SimpleDateTimeForDoctrine\SimpleTimeType;
use PHPUnit\Framework\TestCase;

/**
 * Base class for functional database tests.
 *
 * Supports parallel test execution via paratest by using TEST_TOKEN
 * environment variable to create unique table names per test process.
 *
 * @internal
 */
abstract class AbstractDatabaseTestCase extends TestCase
{
    private const string TABLE_NAME = 'simple_datetime_test';
    protected Connection $connection;

    public static function setUpBeforeClass(): void
    {
        self::registerTypes();
    }

    protected function setUp(): void
    {
        $this->connection = $this->createConnection();
        $this->createTestTable();
    }

    protected function tearDown(): void
    {
        $this->dropTestTable();
        $this->connection->close();
    }

    public function testSimpleDateRoundTrip(): void
    {
        $original = new SimpleDate('2025-06-15');
        $type = Type::getType(SimpleDateType::NAME);

        $dbValue = $type->convertToDatabaseValue($original, $this->connection->getDatabasePlatform());
        $id = $this->insertRow($dbValue, null);
        $row = $this->fetchRow($id);

        $restored = $type->convertToPHPValue($row['date_value'], $this->connection->getDatabasePlatform());

        $this->assertInstanceOf(SimpleDate::class, $restored);
        $this->assertTrue($original->isSame($restored));
        $this->assertSame('2025-06-15', $restored->format('Y-m-d'));
    }

    public function testSimpleDateImmutableRoundTrip(): void
    {
        $original = new SimpleDateImmutable('2025-12-25');
        $type = Type::getType(SimpleDateImmutableType::NAME);

        $dbValue = $type->convertToDatabaseValue($original, $this->connection->getDatabasePlatform());
        $id = $this->insertRow($dbValue, null);
        $row = $this->fetchRow($id);

        $restored = $type->convertToPHPValue($row['date_value'], $this->connection->getDatabasePlatform());

        $this->assertInstanceOf(SimpleDateImmutable::class, $restored);
        $this->assertTrue($original->isSame($restored));
        $this->assertSame('2025-12-25', $restored->format('Y-m-d'));
    }

    public function testSimpleTimeRoundTrip(): void
    {
        $original = new SimpleTime('14:30:45');
        $type = Type::getType(SimpleTimeType::NAME);

        $dbValue = $type->convertToDatabaseValue($original, $this->connection->getDatabasePlatform());
        $id = $this->insertRow(null, $dbValue);
        $row = $this->fetchRow($id);

        $restored = $type->convertToPHPValue($row['time_value'], $this->connection->getDatabasePlatform());

        $this->assertInstanceOf(SimpleTime::class, $restored);
        $this->assertTrue($original->isSame($restored));
        $this->assertSame('14:30:45', $restored->format('H:i:s'));
    }

    public function testSimpleTimeImmutableRoundTrip(): void
    {
        $original = new SimpleTimeImmutable('23:59:59');
        $type = Type::getType(SimpleTimeImmutableType::NAME);

        $dbValue = $type->convertToDatabaseValue($original, $this->connection->getDatabasePlatform());
        $id = $this->insertRow(null, $dbValue);
        $row = $this->fetchRow($id);

        $restored = $type->convertToPHPValue($row['time_value'], $this->connection->getDatabasePlatform());

        $this->assertInstanceOf(SimpleTimeImmutable::class, $restored);
        $this->assertTrue($original->isSame($restored));
        $this->assertSame('23:59:59', $restored->format('H:i:s'));
    }

    public function testNullValuesRoundTrip(): void
    {
        $dateType = Type::getType(SimpleDateType::NAME);
        $timeType = Type::getType(SimpleTimeType::NAME);

        $id = $this->insertRow(null, null);
        $row = $this->fetchRow($id);

        $this->assertNull($dateType->convertToPHPValue($row['date_value'], $this->connection->getDatabasePlatform()));
        $this->assertNull($timeType->convertToPHPValue($row['time_value'], $this->connection->getDatabasePlatform()));
    }

    public function testBoundaryDates(): void
    {
        $type = Type::getType(SimpleDateImmutableType::NAME);
        $platform = $this->connection->getDatabasePlatform();

        $dates = [
            '1970-01-01', // Unix epoch
            '2000-02-29', // Leap year
            '2099-12-31', // Far future
        ];

        foreach ($dates as $dateString) {
            $original = new SimpleDateImmutable($dateString);
            $dbValue = $type->convertToDatabaseValue($original, $platform);
            $id = $this->insertRow($dbValue, null);
            $row = $this->fetchRow($id);
            $restored = $type->convertToPHPValue($row['date_value'], $platform);

            $this->assertInstanceOf(SimpleDateImmutable::class, $restored);
            $this->assertSame($dateString, $restored->format('Y-m-d'), "Failed for date: {$dateString}");
        }
    }

    public function testBoundaryTimes(): void
    {
        $type = Type::getType(SimpleTimeImmutableType::NAME);
        $platform = $this->connection->getDatabasePlatform();

        $times = [
            '00:00:00', // Midnight
            '12:00:00', // Noon
            '23:59:59', // End of day
        ];

        foreach ($times as $timeString) {
            $original = new SimpleTimeImmutable($timeString);
            $dbValue = $type->convertToDatabaseValue($original, $platform);
            $id = $this->insertRow(null, $dbValue);
            $row = $this->fetchRow($id);
            $restored = $type->convertToPHPValue($row['time_value'], $platform);

            $this->assertInstanceOf(SimpleTimeImmutable::class, $restored);
            $this->assertSame($timeString, $restored->format('H:i:s'), "Failed for time: {$timeString}");
        }
    }

    abstract protected function createConnection(): Connection;

    abstract protected function getDateColumnType(): string;

    abstract protected function getTimeColumnType(): string;

    /**
     * Returns a unique table name for parallel test execution.
     *
     * When running with paratest, TEST_TOKEN differentiates processes.
     */
    protected function getTableName(): string
    {
        $token = getenv('TEST_TOKEN') ?: '';

        return self::TABLE_NAME.('' !== $token ? "_{$token}" : '');
    }

    protected function insertRow(?string $dateValue, ?string $timeValue): int
    {
        $tableName = $this->getTableName();

        $this->connection->executeStatement(
            "INSERT INTO {$tableName} (date_value, time_value) VALUES (:date, :time)",
            ['date' => $dateValue, 'time' => $timeValue]
        );

        return (int) $this->connection->lastInsertId();
    }

    /**
     * @return array{date_value: null|string, time_value: null|string}
     */
    protected function fetchRow(int $id): array
    {
        $tableName = $this->getTableName();

        /** @var array{date_value: null|string, time_value: null|string}|false $result */
        $result = $this->connection->fetchAssociative(
            "SELECT date_value, time_value FROM {$tableName} WHERE id = :id",
            ['id' => $id]
        );

        if (false === $result) {
            throw new \RuntimeException("Row with id {$id} not found");
        }

        return $result;
    }

    private static function registerTypes(): void
    {
        if (!Type::hasType(SimpleDateType::NAME)) {
            Type::addType(SimpleDateType::NAME, SimpleDateType::class);
        }
        if (!Type::hasType(SimpleDateImmutableType::NAME)) {
            Type::addType(SimpleDateImmutableType::NAME, SimpleDateImmutableType::class);
        }
        if (!Type::hasType(SimpleTimeType::NAME)) {
            Type::addType(SimpleTimeType::NAME, SimpleTimeType::class);
        }
        if (!Type::hasType(SimpleTimeImmutableType::NAME)) {
            Type::addType(SimpleTimeImmutableType::NAME, SimpleTimeImmutableType::class);
        }
    }

    private function createTestTable(): void
    {
        $dateType = $this->getDateColumnType();
        $timeType = $this->getTimeColumnType();
        $tableName = $this->getTableName();

        $this->connection->executeStatement(<<<SQL
            CREATE TABLE IF NOT EXISTS {$tableName} (
                id SERIAL PRIMARY KEY,
                date_value {$dateType},
                time_value {$timeType}
            )
            SQL);
    }

    private function dropTestTable(): void
    {
        $tableName = $this->getTableName();
        $this->connection->executeStatement("DROP TABLE IF EXISTS {$tableName}");
    }
}
