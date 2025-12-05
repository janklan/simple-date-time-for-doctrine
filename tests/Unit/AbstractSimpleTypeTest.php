<?php

declare(strict_types=1);

namespace JanKlan\SimpleDateTimeForDoctrine\Tests\Unit;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use JanKlan\SimpleDateTimeForDoctrine\AbstractSimpleType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(AbstractSimpleType::class)]
#[Group('unit')]
class AbstractSimpleTypeTest extends TestCase
{
    private PostgreSQLPlatform $platform;

    protected function setUp(): void
    {
        $this->platform = new PostgreSQLPlatform();
    }

    public function testConvertToDatabaseValueWithStringableObject(): void
    {
        $type = $this->createTestType();
        $stringable = new class implements \Stringable {
            public function __toString(): string
            {
                return '2025-01-15';
            }
        };

        $result = $type->convertToDatabaseValue($stringable, $this->platform);

        $this->assertSame('2025-01-15', $result);
    }

    public function testGetNameThrowsWhenNameConstantNotDefined(): void
    {
        $type = new class extends AbstractSimpleType {
            public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
            {
                return 'DATE';
            }

            protected function getFormat(): string
            {
                return 'Y-m-d';
            }

            protected function getTargetClass(): string
            {
                return \DateTimeImmutable::class;
            }

            protected function createFromString(string $value): object
            {
                return new \DateTimeImmutable($value);
            }
        };

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("doesn't define the NAME constant");

        $type->getName();
    }

    private function createTestType(): AbstractSimpleType
    {
        return new class extends AbstractSimpleType {
            public const string NAME = 'test_type';

            public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
            {
                return 'DATE';
            }

            protected function getFormat(): string
            {
                return 'Y-m-d';
            }

            protected function getTargetClass(): string
            {
                return \DateTimeImmutable::class;
            }

            protected function createFromString(string $value): object
            {
                return new \DateTimeImmutable($value);
            }
        };
    }
}
