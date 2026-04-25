<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Date;

use DateTimeImmutable;
use Danilovl\HelperUtils\Exception\InvalidDateException;
use Danilovl\HelperUtils\Helper\Date\DateHelper;
use PHPUnit\Framework\TestCase;
use IntlDateFormatter;

final class DateHelperTest extends TestCase
{
    public function testParseValidDate(): void
    {
        $date = DateHelper::parse('2026-04-25');
        self::assertSame('2026-04-25', $date->format('Y-m-d'));
    }

    public function testParseWithFormat(): void
    {
        $date = DateHelper::parse('25/04/2026', 'd/m/Y');
        self::assertSame('2026-04-25', $date->format('Y-m-d'));
    }

    public function testParseWithTimezone(): void
    {
        $date = DateHelper::parse('2026-04-25 12:00:00', null, 'Europe/Prague');
        self::assertSame('Europe/Prague', $date->getTimezone()->getName());
    }

    public function testParseInvalidThrows(): void
    {
        $this->expectException(InvalidDateException::class);
        DateHelper::parse('not a date');
    }

    public function testParseWithFormatMismatchThrows(): void
    {
        $this->expectException(InvalidDateException::class);
        DateHelper::parse('25/04/2026', 'Y-m-d');
    }

    public function testTryParseReturnsNullOnInvalid(): void
    {
        self::assertNull(DateHelper::tryParse('not a date'));
    }

    public function testTryParseReturnsValid(): void
    {
        self::assertInstanceOf(DateTimeImmutable::class, DateHelper::tryParse('2026-04-25'));
    }

    public function testParseFlexibleFormats(): void
    {
        self::assertSame('2026-04-25', DateHelper::parseFlexible('2026-04-25')->format('Y-m-d'));
        self::assertSame('2026-04-25', DateHelper::parseFlexible('25.04.2026')->format('Y-m-d'));
        self::assertSame('2026-04-25', DateHelper::parseFlexible('25/04/2026')->format('Y-m-d'));
        self::assertSame('2026-04-25 12:30:00', DateHelper::parseFlexible('2026-04-25 12:30:00')->format('Y-m-d H:i:s'));
        self::assertSame('2026-04-25 12:30:00', DateHelper::parseFlexible('2026-04-25T12:30:00')->format('Y-m-d H:i:s'));
        self::assertSame('2026-04-25 12:30:00', DateHelper::parseFlexible('25.04.2026 12:30:00')->format('Y-m-d H:i:s'));
        self::assertSame('2026-04-25', DateHelper::parseFlexible('2026/04/25')->format('Y-m-d'));
        self::assertSame('2026-04-25', DateHelper::parseFlexible('2026.04.25')->format('Y-m-d'));
        self::assertSame('2026-04-25 12:30', DateHelper::parseFlexible('2026-04-25 12:30')->format('Y-m-d H:i'));
    }

    public function testParseFlexibleThrows(): void
    {
        $this->expectException(InvalidDateException::class);
        DateHelper::parseFlexible('not a date');
    }

    public function testIsValid(): void
    {
        self::assertTrue(DateHelper::isValid('2026-04-25'));
        self::assertFalse(DateHelper::isValid('2026-13-25'));
        self::assertFalse(DateHelper::isValid('not a date'));
    }

    public function testIsValidIso8601(): void
    {
        self::assertTrue(DateHelper::isValidIso8601('2026-04-25'));
        self::assertTrue(DateHelper::isValidIso8601('2026-04-25T12:30:00Z'));
        self::assertTrue(DateHelper::isValidIso8601('2026-04-25T12:30:00+02:00'));
        self::assertFalse(DateHelper::isValidIso8601('25/04/2026'));
    }

    public function testIsPast(): void
    {
        $now = new DateTimeImmutable('2026-04-25');
        self::assertTrue(DateHelper::isPast(new DateTimeImmutable('2020-01-01'), $now));
        self::assertFalse(DateHelper::isPast(new DateTimeImmutable('2030-01-01'), $now));
    }

    public function testIsFuture(): void
    {
        $now = new DateTimeImmutable('2026-04-25');
        self::assertTrue(DateHelper::isFuture(new DateTimeImmutable('2030-01-01'), $now));
        self::assertFalse(DateHelper::isFuture(new DateTimeImmutable('2020-01-01'), $now));
    }

    public function testIsTodayWithProvidedNow(): void
    {
        $now = new DateTimeImmutable('2026-04-25 14:00:00');
        self::assertTrue(DateHelper::isToday(new DateTimeImmutable('2026-04-25 02:00:00'), $now));
        self::assertFalse(DateHelper::isToday(new DateTimeImmutable('2026-04-24 23:59:59'), $now));
    }

    public function testIsSameDayWeekMonth(): void
    {
        $a = new DateTimeImmutable('2026-04-20 09:00:00');
        $b = new DateTimeImmutable('2026-04-20 21:00:00');
        $c = new DateTimeImmutable('2026-04-22 10:00:00');
        self::assertTrue(DateHelper::isSameDay($a, $b));
        self::assertFalse(DateHelper::isSameDay($a, $c));
        self::assertTrue(DateHelper::isSameWeek($a, $c));
        self::assertTrue(DateHelper::isSameMonth($a, $c));
    }

    public function testIsInCurrentWeekMonth(): void
    {
        $now = new DateTimeImmutable('2026-04-25'); // Saturday
        $sameWeek = new DateTimeImmutable('2026-04-22');
        $differentWeek = new DateTimeImmutable('2026-04-18');
        $sameMonth = new DateTimeImmutable('2026-04-01');
        $differentMonth = new DateTimeImmutable('2026-03-31');

        self::assertTrue(DateHelper::isInCurrentWeek($sameWeek, $now));
        self::assertFalse(DateHelper::isInCurrentWeek($differentWeek, $now));
        self::assertTrue(DateHelper::isInCurrentMonth($sameMonth, $now));
        self::assertFalse(DateHelper::isInCurrentMonth($differentMonth, $now));

        self::assertTrue(DateHelper::isInCurrentWeek(new DateTimeImmutable));
        self::assertTrue(DateHelper::isInCurrentMonth(new DateTimeImmutable));
    }

    public function testWeekdayWeekend(): void
    {
        $sat = new DateTimeImmutable('2026-04-25');
        $mon = new DateTimeImmutable('2026-04-27');
        self::assertTrue(DateHelper::isWeekend($sat));
        self::assertFalse(DateHelper::isWeekday($sat));
        self::assertFalse(DateHelper::isWeekend($mon));
        self::assertTrue(DateHelper::isWeekday($mon));
    }

    public function testStartEndOfDay(): void
    {
        $date = new DateTimeImmutable('2026-04-25 14:30:45');
        self::assertSame('2026-04-25 00:00:00', DateHelper::startOfDay($date)->format('Y-m-d H:i:s'));
        self::assertSame('2026-04-25 23:59:59', DateHelper::endOfDay($date)->format('Y-m-d H:i:s'));
    }

    public function testStartEndOfWeekMonday(): void
    {
        $wed = new DateTimeImmutable('2026-04-22 14:30:00'); // Wednesday
        self::assertSame('2026-04-20', DateHelper::startOfWeek($wed)->format('Y-m-d'));
        self::assertSame('2026-04-26', DateHelper::endOfWeek($wed)->format('Y-m-d'));
    }

    public function testStartEndOfWeekSunday(): void
    {
        $wed = new DateTimeImmutable('2026-04-22');
        self::assertSame('2026-04-19', DateHelper::startOfWeek($wed, 'sunday')->format('Y-m-d'));
        self::assertSame('2026-04-25', DateHelper::endOfWeek($wed, 'sunday')->format('Y-m-d'));
    }

    public function testStartEndOfMonth(): void
    {
        $date = new DateTimeImmutable('2026-04-15 12:00:00');
        self::assertSame('2026-04-01 00:00:00', DateHelper::startOfMonth($date)->format('Y-m-d H:i:s'));
        self::assertSame('2026-04-30 23:59:59', DateHelper::endOfMonth($date)->format('Y-m-d H:i:s'));
    }

    public function testStartEndOfYear(): void
    {
        $date = new DateTimeImmutable('2026-04-15');
        self::assertSame('2026-01-01', DateHelper::startOfYear($date)->format('Y-m-d'));
        self::assertSame('2026-12-31', DateHelper::endOfYear($date)->format('Y-m-d'));
    }

    public function testNavigation(): void
    {
        $date = new DateTimeImmutable('2026-04-25');
        self::assertSame('2026-05-02', DateHelper::nextWeek($date)->format('Y-m-d'));
        self::assertSame('2026-04-18', DateHelper::previousWeek($date)->format('Y-m-d'));
        self::assertSame('2026-05-25', DateHelper::nextMonth($date)->format('Y-m-d'));
        self::assertSame('2026-03-25', DateHelper::previousMonth($date)->format('Y-m-d'));
    }

    public function testDiffMethods(): void
    {
        $a = new DateTimeImmutable('2026-04-01 00:00:00');
        $b = new DateTimeImmutable('2026-04-02 12:00:00');
        self::assertSame(36 * 3_600, DateHelper::diffInSeconds($a, $b));
        self::assertSame(36 * 60, DateHelper::diffInMinutes($a, $b));
        self::assertSame(36, DateHelper::diffInHours($a, $b));
        self::assertSame(1, DateHelper::diffInDays($a, $b));
    }

    public function testDiffNegative(): void
    {
        $a = new DateTimeImmutable('2026-04-25');
        $b = new DateTimeImmutable('2026-04-20');
        self::assertSame(-5, DateHelper::diffInDays($a, $b));
    }

    public function testDiffInWeeksMonthsYears(): void
    {
        $a = new DateTimeImmutable('2024-01-15');
        $b = new DateTimeImmutable('2026-04-15');
        self::assertSame(2, DateHelper::diffInYears($a, $b));
        self::assertSame(27, DateHelper::diffInMonths($a, $b));
        self::assertSame(117, DateHelper::diffInWeeks($a, $b));
    }

    public function testCalculateAge(): void
    {
        $birthday = new DateTimeImmutable('1990-04-25');
        $now = new DateTimeImmutable('2026-04-24');
        self::assertSame(35, DateHelper::calculateAge($birthday, $now));

        $now = new DateTimeImmutable('2026-04-25');
        self::assertSame(36, DateHelper::calculateAge($birthday, $now));
    }

    public function testGetQuarter(): void
    {
        self::assertSame(1, DateHelper::getQuarter(new DateTimeImmutable('2026-01-15')));
        self::assertSame(2, DateHelper::getQuarter(new DateTimeImmutable('2026-04-15')));
        self::assertSame(3, DateHelper::getQuarter(new DateTimeImmutable('2026-07-15')));
        self::assertSame(4, DateHelper::getQuarter(new DateTimeImmutable('2026-12-15')));
    }

    public function testGetDayOfWeek(): void
    {
        self::assertSame(6, DateHelper::getDayOfWeek(new DateTimeImmutable('2026-04-25'))); // Saturday
        self::assertSame(1, DateHelper::getDayOfWeek(new DateTimeImmutable('2026-04-27'))); // Monday
    }

    public function testFormatIso(): void
    {
        $date = new DateTimeImmutable('2026-04-25T12:30:00+00:00');
        self::assertSame('2026-04-25T12:30:00+00:00', DateHelper::formatIso($date));
    }

    public function testFormatRelativePast(): void
    {
        $now = new DateTimeImmutable('2026-04-25 12:00:00');
        $earlier = new DateTimeImmutable('2026-04-25 10:00:00');
        self::assertSame('2 hours ago', DateHelper::formatRelative($earlier, $now));
    }

    public function testFormatRelativeFuture(): void
    {
        $now = new DateTimeImmutable('2026-04-25 12:00:00');
        $later = new DateTimeImmutable('2026-04-28 12:00:00');
        self::assertSame('in 3 days', DateHelper::formatRelative($later, $now));
    }

    public function testFormatRelativeSingularPlural(): void
    {
        $now = new DateTimeImmutable('2026-04-25 12:00:00');
        self::assertSame('1 minute ago', DateHelper::formatRelative(new DateTimeImmutable('2026-04-25 11:59:00'), $now));
        self::assertSame('5 minutes ago', DateHelper::formatRelative(new DateTimeImmutable('2026-04-25 11:55:00'), $now));
    }

    public function testFormatRelativeVariousUnits(): void
    {
        $now = new DateTimeImmutable('2026-04-25 12:00:00');

        self::assertSame('30 seconds ago', DateHelper::formatRelative(new DateTimeImmutable('2026-04-25 11:59:30'), $now));
        self::assertSame('2 months ago', DateHelper::formatRelative(new DateTimeImmutable('2026-02-20 12:00:00'), $now));
        self::assertSame('2 years ago', DateHelper::formatRelative(new DateTimeImmutable('2024-04-25 12:00:00'), $now));
    }

    public function testFormatHumanDate(): void
    {
        $date = new DateTimeImmutable('2026-04-25');
        $result = DateHelper::formatHumanDate($date, 'en');
        self::assertNotEmpty($result);

        if (!class_exists(IntlDateFormatter::class)) {
            self::assertSame('April 25, 2026', $result);
        } else {
            self::assertStringContainsString('April', $result);
            self::assertStringContainsString('25', $result);
            self::assertStringContainsString('2026', $result);
        }
    }
}
