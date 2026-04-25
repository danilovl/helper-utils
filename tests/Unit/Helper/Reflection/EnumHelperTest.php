<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Reflection;

use Danilovl\HelperUtils\Exception\HelperException;
use Danilovl\HelperUtils\Helper\Reflection\EnumHelper;
use PHPUnit\Framework\TestCase;
use UnitEnum;

enum TestStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';
}

enum TestPriority: int
{
    case LOW = 1;
    case HIGH = 10;
}

enum TestColor
{
    case RED;
    case GREEN;
    case BLUE;
}

final class EnumHelperTest extends TestCase
{
    public function testValuesString(): void
    {
        self::assertSame(['active', 'inactive', 'pending'], EnumHelper::values(TestStatus::class));
    }

    public function testValuesInt(): void
    {
        self::assertSame([1, 10], EnumHelper::values(TestPriority::class));
    }

    public function testValuesNonBackedThrows(): void
    {
        $this->expectException(HelperException::class);
        /** @phpstan-ignore argument.type */
        EnumHelper::values(TestColor::class);
    }

    public function testNamesBacked(): void
    {
        self::assertSame(['ACTIVE', 'INACTIVE', 'PENDING'], EnumHelper::names(TestStatus::class));
    }

    public function testNamesUnbacked(): void
    {
        self::assertSame(['RED', 'GREEN', 'BLUE'], EnumHelper::names(TestColor::class));
    }

    public function testTryFromName(): void
    {
        self::assertSame(TestStatus::ACTIVE, EnumHelper::tryFromName(TestStatus::class, 'ACTIVE'));
        self::assertNull(EnumHelper::tryFromName(TestStatus::class, 'MISSING'));
    }

    public function testExists(): void
    {
        self::assertTrue(EnumHelper::exists(TestStatus::class, 'ACTIVE'));
        self::assertFalse(EnumHelper::exists(TestStatus::class, 'MISSING'));
    }

    public function testRandom(): void
    {
        $value = EnumHelper::random(TestStatus::class);
        self::assertContains($value, TestStatus::cases());
    }

    public function testToChoicesBacked(): void
    {
        $choices = EnumHelper::toChoices(TestStatus::class);
        self::assertSame(['active' => 'ACTIVE', 'inactive' => 'INACTIVE', 'pending' => 'PENDING'], $choices);
    }

    public function testToChoicesUnbacked(): void
    {
        $choices = EnumHelper::toChoices(TestColor::class);
        self::assertSame(['RED' => 'RED', 'GREEN' => 'GREEN', 'BLUE' => 'BLUE'], $choices);
    }

    public function testToChoicesWithLabelFn(): void
    {
        $labelFn = static fn (UnitEnum $s): string => mb_strtolower($s->name);
        $choices = EnumHelper::toChoices(TestStatus::class, $labelFn);
        self::assertSame(['active' => 'active', 'inactive' => 'inactive', 'pending' => 'pending'], $choices);
    }

    public function testInvalidEnumThrows(): void
    {
        $this->expectException(HelperException::class);
        /** @phpstan-ignore-next-line */
        EnumHelper::names('NotAnEnum');
    }
}
