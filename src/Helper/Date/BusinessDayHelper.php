<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Date;

use DateTimeImmutable;

final class BusinessDayHelper
{
    /**
     * @param list<DateTimeImmutable|string> $holidays
     */
    public static function isBusinessDay(DateTimeImmutable $date, array $holidays = []): bool
    {
        if (DateHelper::isWeekend($date)) {
            return false;
        }

        $dateString = $date->format('Y-m-d');
        foreach ($holidays as $holiday) {
            $holidayString = $holiday instanceof DateTimeImmutable
                ? $holiday->format('Y-m-d')
                : $holiday;

            if ($holidayString === $dateString) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param list<DateTimeImmutable|string> $holidays
     */
    public static function addBusinessDays(DateTimeImmutable $date, int $days, array $holidays = []): DateTimeImmutable
    {
        if ($days === 0) {
            return $date;
        }

        $direction = $days > 0 ? '+1 day' : '-1 day';
        $remaining = abs($days);
        $result = $date;

        while ($remaining > 0) {
            $result = $result->modify($direction);
            if (self::isBusinessDay($result, $holidays)) {
                $remaining--;
            }
        }

        return $result;
    }

    /**
     * @param list<DateTimeImmutable|string> $holidays
     */
    public static function nextBusinessDay(DateTimeImmutable $date, array $holidays = []): DateTimeImmutable
    {
        return self::addBusinessDays($date, 1, $holidays);
    }

    /**
     * @param list<DateTimeImmutable|string> $holidays
     */
    public static function businessDaysBetween(
        DateTimeImmutable $a,
        DateTimeImmutable $b,
        array $holidays = []
    ): int {
        if ($a > $b) {
            return -self::businessDaysBetween($b, $a, $holidays);
        }

        $count = 0;
        $current = DateHelper::startOfDay($a);
        $end = DateHelper::startOfDay($b);

        while ($current < $end) {
            $current = $current->modify('+1 day');
            if (self::isBusinessDay($current, $holidays)) {
                $count++;
            }
        }

        return $count;
    }
}
