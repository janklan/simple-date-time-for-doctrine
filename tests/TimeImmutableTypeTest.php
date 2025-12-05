<?php

declare(strict_types=1);

namespace JanKlan\SimpleDateTimeDoctrine\Tests;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Type;
use JanKlan\SimpleDateTime\TimeImmutable;
use JanKlan\SimpleDateTimeDoctrine\TimeImmutableType;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
class TimeImmutableTypeTest extends TestCase
{
    private TimeImmutableType $type;

    private PostgreSQLPlatform $platform;

    public static function setUpBeforeClass(): void
    {
        if (!Type::hasType(TimeImmutableType::NAME)) {
            Type::addType(TimeImmutableType::NAME, TimeImmutableType::class);
        }
    }

    protected function setUp(): void
    {
        $type = Type::getType(TimeImmutableType::NAME);
        \assert($type instanceof TimeImmutableType);
        $this->type = $type;
        $this->platform = new PostgreSQLPlatform();
    }

    public function testGetName(): void
    {
        $this->assertSame('simple_time_immutable', $this->type->getName());
    }

    public function testGetSQLDeclaration(): void
    {
        $sql = $this->type->getSQLDeclaration([], $this->platform);
        $this->assertSame('TIME(0) WITHOUT TIME ZONE', $sql);
    }

    public function testConvertToPHPValueWithNull(): void
    {
        $result = $this->type->convertToPHPValue(null, $this->platform);
        $this->assertNull($result);
    }

    public function testConvertToPHPValueWithEmptyString(): void
    {
        $result = $this->type->convertToPHPValue('', $this->platform);
        $this->assertNull($result);
    }

    public function testConvertToPHPValueWithString(): void
    {
        $result = $this->type->convertToPHPValue('14:30:45', $this->platform);

        $this->assertInstanceOf(TimeImmutable::class, $result);
        $this->assertSame('14:30:45', $result->format('H:i:s'));
    }

    public function testConvertToPHPValueWithTimeImmutableInstance(): void
    {
        $time = new TimeImmutable('16:45:00');
        $result = $this->type->convertToPHPValue($time, $this->platform);

        $this->assertSame($time, $result);
    }

    public function testConvertToDatabaseValueWithNull(): void
    {
        $result = $this->type->convertToDatabaseValue(null, $this->platform);
        $this->assertNull($result);
    }

    public function testConvertToDatabaseValueWithTimeImmutable(): void
    {
        $time = new TimeImmutable('14:30:45');
        $result = $this->type->convertToDatabaseValue($time, $this->platform);

        $this->assertSame('14:30:45', $result);
    }

    public function testConvertToDatabaseValueWithDateTime(): void
    {
        $dateTime = new \DateTime('2025-03-10 09:15:30');
        $result = $this->type->convertToDatabaseValue($dateTime, $this->platform);

        $this->assertSame('09:15:30', $result);
    }

    public function testConvertToDatabaseValueWithDateTimeImmutable(): void
    {
        $dateTime = new \DateTimeImmutable('2025-08-25 23:59:59');
        $result = $this->type->convertToDatabaseValue($dateTime, $this->platform);

        $this->assertSame('23:59:59', $result);
    }

    public function testRoundTrip(): void
    {
        $original = new TimeImmutable('14:30:45');
        $dbValue = $this->type->convertToDatabaseValue($original, $this->platform);
        $restored = $this->type->convertToPHPValue($dbValue, $this->platform);

        $this->assertInstanceOf(TimeImmutable::class, $restored);
        $this->assertTrue($original->isSameTimeAs($restored));
    }
}
