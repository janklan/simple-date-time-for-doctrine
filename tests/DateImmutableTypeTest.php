<?php

declare(strict_types=1);

namespace JanKlan\SimpleDateTimeDoctrine\Tests;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Type;
use JanKlan\SimpleDateTime\DateImmutable;
use JanKlan\SimpleDateTimeDoctrine\DateImmutableType;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
class DateImmutableTypeTest extends TestCase
{
    private DateImmutableType $type;

    private PostgreSQLPlatform $platform;

    public static function setUpBeforeClass(): void
    {
        if (!Type::hasType(DateImmutableType::NAME)) {
            Type::addType(DateImmutableType::NAME, DateImmutableType::class);
        }
    }

    protected function setUp(): void
    {
        $type = Type::getType(DateImmutableType::NAME);
        \assert($type instanceof DateImmutableType);
        $this->type = $type;
        $this->platform = new PostgreSQLPlatform();
    }

    public function testGetName(): void
    {
        $this->assertSame('simple_date_immutable', $this->type->getName());
    }

    public function testGetSQLDeclaration(): void
    {
        $sql = $this->type->getSQLDeclaration([], $this->platform);
        $this->assertSame('DATE', $sql);
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
        $result = $this->type->convertToPHPValue('2025-01-15', $this->platform);

        $this->assertInstanceOf(DateImmutable::class, $result);
        $this->assertSame('2025-01-15', $result->format('Y-m-d'));
    }

    public function testConvertToPHPValueWithDateImmutableInstance(): void
    {
        $date = new DateImmutable('2025-06-20');
        $result = $this->type->convertToPHPValue($date, $this->platform);

        $this->assertSame($date, $result);
    }

    public function testConvertToDatabaseValueWithNull(): void
    {
        $result = $this->type->convertToDatabaseValue(null, $this->platform);
        $this->assertNull($result);
    }

    public function testConvertToDatabaseValueWithDateImmutable(): void
    {
        $date = new DateImmutable('2025-01-15');
        $result = $this->type->convertToDatabaseValue($date, $this->platform);

        $this->assertSame('2025-01-15', $result);
    }

    public function testConvertToDatabaseValueWithDateTime(): void
    {
        $dateTime = new \DateTime('2025-03-10 14:30:00');
        $result = $this->type->convertToDatabaseValue($dateTime, $this->platform);

        $this->assertSame('2025-03-10', $result);
    }

    public function testConvertToDatabaseValueWithDateTimeImmutable(): void
    {
        $dateTime = new \DateTimeImmutable('2025-08-25 09:15:30');
        $result = $this->type->convertToDatabaseValue($dateTime, $this->platform);

        $this->assertSame('2025-08-25', $result);
    }

    public function testRoundTrip(): void
    {
        $original = new DateImmutable('2025-12-25');
        $dbValue = $this->type->convertToDatabaseValue($original, $this->platform);
        $restored = $this->type->convertToPHPValue($dbValue, $this->platform);

        $this->assertInstanceOf(DateImmutable::class, $restored);
        $this->assertTrue($original->isSameDateAs($restored));
    }
}
