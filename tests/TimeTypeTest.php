<?php

declare(strict_types=1);

namespace JanKlan\SimpleDateTimeDoctrine\Tests;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Type;
use JanKlan\SimpleDateTime\Time;
use JanKlan\SimpleDateTimeDoctrine\TimeType;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
class TimeTypeTest extends TestCase
{
    private TimeType $type;

    private PostgreSQLPlatform $platform;

    public static function setUpBeforeClass(): void
    {
        if (!Type::hasType(TimeType::NAME)) {
            Type::addType(TimeType::NAME, TimeType::class);
        }
    }

    protected function setUp(): void
    {
        $type = Type::getType(TimeType::NAME);
        \assert($type instanceof TimeType);
        $this->type = $type;
        $this->platform = new PostgreSQLPlatform();
    }

    public function testGetName(): void
    {
        $this->assertSame('simple_time', $this->type->getName());
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

        $this->assertInstanceOf(Time::class, $result);
        $this->assertSame('14:30:45', $result->format('H:i:s'));
    }

    public function testConvertToPHPValueWithTimeInstance(): void
    {
        $time = new Time('16:45:00');
        $result = $this->type->convertToPHPValue($time, $this->platform);

        $this->assertSame($time, $result);
    }

    public function testConvertToDatabaseValueWithNull(): void
    {
        $result = $this->type->convertToDatabaseValue(null, $this->platform);
        $this->assertNull($result);
    }

    public function testConvertToDatabaseValueWithTime(): void
    {
        $time = new Time('14:30:45');
        $result = $this->type->convertToDatabaseValue($time, $this->platform);

        $this->assertSame('14:30:45', $result);
    }

    public function testConvertToDatabaseValueWithDateTime(): void
    {
        $dateTime = new \DateTime('2025-03-10 09:15:30');
        $result = $this->type->convertToDatabaseValue($dateTime, $this->platform);

        $this->assertSame('09:15:30', $result);
    }

    public function testRoundTrip(): void
    {
        $original = new Time('14:30:45');
        $dbValue = $this->type->convertToDatabaseValue($original, $this->platform);
        $restored = $this->type->convertToPHPValue($dbValue, $this->platform);

        $this->assertInstanceOf(Time::class, $restored);
        $this->assertTrue($original->isSameTimeAs($restored));
    }
}
