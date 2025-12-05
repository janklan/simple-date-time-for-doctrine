<?php

declare(strict_types=1);

namespace JanKlan\SimpleDateTimeForDoctrine\Tests\Unit;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Type;
use JanKlan\SimpleDateTime\SimpleDate;
use JanKlan\SimpleDateTimeForDoctrine\AbstractSimpleDateType;
use JanKlan\SimpleDateTimeForDoctrine\AbstractSimpleType;
use JanKlan\SimpleDateTimeForDoctrine\SimpleDateType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SimpleDateType::class)]
#[CoversClass(AbstractSimpleDateType::class)]
#[CoversClass(AbstractSimpleType::class)]
#[Group('unit')]
class SimpleDateTypeTest extends TestCase
{
    private SimpleDateType $type;

    private PostgreSQLPlatform $platform;

    public static function setUpBeforeClass(): void
    {
        if (!Type::hasType(SimpleDateType::NAME)) {
            Type::addType(SimpleDateType::NAME, SimpleDateType::class);
        }
    }

    protected function setUp(): void
    {
        $type = Type::getType(SimpleDateType::NAME);
        \assert($type instanceof SimpleDateType);
        $this->type = $type;
        $this->platform = new PostgreSQLPlatform();
    }

    public function testGetName(): void
    {
        $this->assertSame('simple_date', $this->type->getName());
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

        $this->assertInstanceOf(SimpleDate::class, $result);
        $this->assertSame('2025-01-15', $result->format('Y-m-d'));
    }

    public function testConvertToPHPValueWithSimpleDateInstance(): void
    {
        $date = new SimpleDate('2025-06-20');
        $result = $this->type->convertToPHPValue($date, $this->platform);

        $this->assertSame($date, $result);
    }

    public function testConvertToDatabaseValueWithNull(): void
    {
        $result = $this->type->convertToDatabaseValue(null, $this->platform);
        $this->assertNull($result);
    }

    public function testConvertToDatabaseValueWithSimpleDate(): void
    {
        $date = new SimpleDate('2025-01-15');
        $result = $this->type->convertToDatabaseValue($date, $this->platform);

        $this->assertSame('2025-01-15', $result);
    }

    public function testConvertToDatabaseValueWithDateTime(): void
    {
        $dateTime = new \DateTime('2025-03-10 14:30:00');
        $result = $this->type->convertToDatabaseValue($dateTime, $this->platform);

        $this->assertSame('2025-03-10', $result);
    }

    public function testRoundTrip(): void
    {
        $original = new SimpleDate('2025-12-25');
        $dbValue = $this->type->convertToDatabaseValue($original, $this->platform);
        $restored = $this->type->convertToPHPValue($dbValue, $this->platform);

        $this->assertInstanceOf(SimpleDate::class, $restored);
        $this->assertTrue($original->isSame($restored));
    }
}
