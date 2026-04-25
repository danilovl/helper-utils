<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Date;

use DateTimeImmutable;
use Danilovl\HelperUtils\Helper\Date\BusinessDayHelper;
use PHPUnit\Framework\TestCase;

final class BusinessDayHelperTest extends TestCase
{
    public function testIsBusinessDayWeekday(): void
    {
        self::assertTrue(BusinessDayHelper::isBusinessDay(new DateTimeImmutable('2026-04-27'))); // Monday
    }

    public function testIsBusinessDayWeekend(): void
    {
        self::assertFalse(BusinessDayHelper::isBusinessDay(new DateTimeImmutable('2026-04-25'))); // Saturday
        self::assertFalse(BusinessDayHelper::isBusinessDay(new DateTimeImmutable('2026-04-26'))); // Sunday
    }

    public function testIsBusinessDayHoliday(): void
    {
        $holidays = ['2026-04-27'];
        self::assertFalse(BusinessDayHelper::isBusinessDay(new DateTimeImmutable('2026-04-27'), $holidays));
    }

    public function testIsBusinessDayHolidayObject(): void
    {
        $holidays = [new DateTimeImmutable('2026-04-27')];
        self::assertFalse(BusinessDayHelper::isBusinessDay(new DateTimeImmutable('2026-04-27'), $holidays));
    }

    public function testAddBusinessDaysSkipsWeekends(): void
    {
        // Friday + 1 business day = next Monday
        $friday = new DateTimeImmutable('2026-04-24');
        $result = BusinessDayHelper::addBusinessDays($friday, 1);
        self::assertSame('2026-04-27', $result->format('Y-m-d'));
    }

    public function testAddBusinessDaysSkipsHolidays(): void
    {
        $friday = new DateTimeImmutable('2026-04-24');
        $result = BusinessDayHelper::addBusinessDays($friday, 1, ['2026-04-27']);
        self::assertSame('2026-04-28', $result->format('Y-m-d'));
    }

    public function testAddBusinessDaysNegative(): void
    {
        $monday = new DateTimeImmutable('2026-04-27');
        $result = BusinessDayHelper::addBusinessDays($monday, -1);
        self::assertSame('2026-04-24', $result->format('Y-m-d'));
    }

    public function testAddBusinessDaysZero(): void
    {
        $sat = new DateTimeImmutable('2026-04-25');
        self::assertSame($sat, BusinessDayHelper::addBusinessDays($sat, 0));
    }

    public function testNextBusinessDay(): void
    {
        $friday = new DateTimeImmutable('2026-04-24');
        self::assertSame('2026-04-27', BusinessDayHelper::nextBusinessDay($friday)->format('Y-m-d'));
    }

    public function testBusinessDaysBetween(): void
    {
        $monday = new DateTimeImmutable('2026-04-20');
        $friday = new DateTimeImmutable('2026-04-24');
        self::assertSame(4, BusinessDayHelper::businessDaysBetween($monday, $friday));
    }

    public function testBusinessDaysBetweenAcrossWeekend(): void
    {
        $friday = new DateTimeImmutable('2026-04-24');
        $tuesday = new DateTimeImmutable('2026-04-28');
        self::assertSame(2, BusinessDayHelper::businessDaysBetween($friday, $tuesday));
    }

    public function testBusinessDaysBetweenReversed(): void
    {
        $monday = new DateTimeImmutable('2026-04-20');
        $friday = new DateTimeImmutable('2026-04-24');
        self::assertSame(-4, BusinessDayHelper::businessDaysBetween($friday, $monday));
    }
}
