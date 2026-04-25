<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Date;

use DateTimeImmutable;
use Danilovl\HelperUtils\Helper\Date\DatePeriodHelper;
use PHPUnit\Framework\TestCase;

final class DatePeriodHelperTest extends TestCase
{
    public function testDaysBetween(): void
    {
        $days = iterator_to_array(DatePeriodHelper::daysBetween(
            new DateTimeImmutable('2026-04-25'),
            new DateTimeImmutable('2026-04-28')
        ), false);

        self::assertCount(4, $days);
        self::assertSame('2026-04-25', $days[0]->format('Y-m-d'));
        self::assertSame('2026-04-28', $days[3]->format('Y-m-d'));
    }

    public function testDaysBetweenSameDay(): void
    {
        $days = iterator_to_array(DatePeriodHelper::daysBetween(
            new DateTimeImmutable('2026-04-25'),
            new DateTimeImmutable('2026-04-25')
        ), false);

        self::assertCount(1, $days);
    }

    public function testWeeksBetween(): void
    {
        $weeks = iterator_to_array(DatePeriodHelper::weeksBetween(
            new DateTimeImmutable('2026-04-01'),
            new DateTimeImmutable('2026-04-22')
        ), false);

        self::assertCount(4, $weeks);
    }

    public function testMonthsBetween(): void
    {
        $months = iterator_to_array(DatePeriodHelper::monthsBetween(
            new DateTimeImmutable('2026-01-15'),
            new DateTimeImmutable('2026-04-15')
        ), false);

        self::assertCount(4, $months);
    }

    public function testIntersect(): void
    {
        $a1 = new DateTimeImmutable('2026-04-01');
        $a2 = new DateTimeImmutable('2026-04-30');
        $b1 = new DateTimeImmutable('2026-04-15');
        $b2 = new DateTimeImmutable('2026-05-15');
        self::assertTrue(DatePeriodHelper::intersect($a1, $a2, $b1, $b2));

        $c1 = new DateTimeImmutable('2026-06-01');
        $c2 = new DateTimeImmutable('2026-06-15');
        self::assertFalse(DatePeriodHelper::intersect($a1, $a2, $c1, $c2));
    }

    public function testOverlap(): void
    {
        $a1 = new DateTimeImmutable('2026-04-01');
        $a2 = new DateTimeImmutable('2026-04-30');
        $b1 = new DateTimeImmutable('2026-04-15');
        $b2 = new DateTimeImmutable('2026-05-15');

        $overlap = DatePeriodHelper::overlap($a1, $a2, $b1, $b2);
        self::assertNotNull($overlap);
        self::assertSame('2026-04-15', $overlap[0]->format('Y-m-d'));
        self::assertSame('2026-04-30', $overlap[1]->format('Y-m-d'));
    }

    public function testOverlapNonIntersecting(): void
    {
        $overlap = DatePeriodHelper::overlap(
            new DateTimeImmutable('2026-04-01'),
            new DateTimeImmutable('2026-04-15'),
            new DateTimeImmutable('2026-05-01'),
            new DateTimeImmutable('2026-05-15')
        );
        self::assertNull($overlap);
    }
}
