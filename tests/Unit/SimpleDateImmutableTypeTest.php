<?php

declare(strict_types=1);

namespace JanKlan\SimpleDateTimeForDoctrine\Tests\Unit;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Type;
use JanKlan\SimpleDateTime\SimpleDateImmutable;
use JanKlan\SimpleDateTimeForDoctrine\AbstractSimpleDateType;
use JanKlan\SimpleDateTimeForDoctrine\AbstractSimpleType;
use JanKlan\SimpleDateTimeForDoctrine\SimpleDateImmutableType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SimpleDateImmutableType::class)]
#[CoversClass(AbstractSimpleDateType::class)]
#[CoversClass(AbstractSimpleType::class)]
#[Group('unit')]
class SimpleDateImmutableTypeTest extends TestCase
{
    private SimpleDateImmutableType $type;

    private PostgreSQLPlatform $platform;

    public static function setUpBeforeClass(): void
    {
        if (!Type::hasType(SimpleDateImmutableType::NAME)) {
            Type::addType(SimpleDateImmutableType::NAME, SimpleDateImmutableType::class);
        }
    }

    protected function setUp(): void
    {
        $type = Type::getType(SimpleDateImmutableType::NAME);
        \assert($type instanceof SimpleDateImmutableType);
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

        $this->assertInstanceOf(SimpleDateImmutable::class, $result);
        $this->assertSame('2025-01-15', $result->format('Y-m-d'));
    }

    public function testConvertToPHPValueWithSimpleDateImmutableInstance(): void
    {
        $date = new SimpleDateImmutable('2025-06-20');
        $result = $this->type->convertToPHPValue($date, $this->platform);

        $this->assertSame($date, $result);
    }

    public function testConvertToDatabaseValueWithNull(): void
    {
        $result = $this->type->convertToDatabaseValue(null, $this->platform);
        $this->assertNull($result);
    }

    public function testConvertToDatabaseValueWithSimpleDateImmutable(): void
    {
        $date = new SimpleDateImmutable('2025-01-15');
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
        $original = new SimpleDateImmutable('2025-12-25');
        $dbValue = $this->type->convertToDatabaseValue($original, $this->platform);
        $restored = $this->type->convertToPHPValue($dbValue, $this->platform);

        $this->assertInstanceOf(SimpleDateImmutable::class, $restored);
        $this->assertTrue($original->isSame($restored));
    }

    public function testGetMappedDatabaseTypes(): void
    {
        $result = $this->type->getMappedDatabaseTypes($this->platform);
        $this->assertSame(['date'], $result);
    }
}
