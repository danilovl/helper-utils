<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Date;

use DateTimeImmutable;
use DateTimeZone;
use Danilovl\HelperUtils\Exception\InvalidDateException;
use Exception;
use IntlDateFormatter;

final class DateHelper
{
    private const array FLEXIBLE_FORMATS = [
        'Y-m-d\TH:i:sP',
        'Y-m-d\TH:i:s',
        'Y-m-d H:i:s',
        'Y-m-d H:i',
        'Y-m-d',
        'd.m.Y H:i:s',
        'd.m.Y H:i',
        'd.m.Y',
        'd/m/Y',
        'm/d/Y',
        'Y/m/d',
        'Y.m.d',
    ];

    public static function parse(string $value, ?string $format = null, ?string $timezone = null): DateTimeImmutable
    {
        $tz = $timezone !== null ? new DateTimeZone($timezone) : null;

        if ($format !== null) {
            $date = DateTimeImmutable::createFromFormat($format, $value, $tz);
            if ($date === false) {
                throw new InvalidDateException(sprintf('Cannot parse "%s" with format "%s".', $value, $format));
            }

            return $date;
        }

        try {
            return new DateTimeImmutable($value, $tz);
        } catch (Exception $e) {
            throw new InvalidDateException(sprintf('Cannot parse date string "%s".', $value), 0, $e);
        }
    }

    public static function tryParse(string $value, ?string $format = null): ?DateTimeImmutable
    {
        try {
            return self::parse($value, $format);
        } catch (InvalidDateException) {
            return null;
        }
    }

    public static function parseFlexible(string $value): DateTimeImmutable
    {
        foreach (self::FLEXIBLE_FORMATS as $format) {
            $date = DateTimeImmutable::createFromFormat($format, $value);
            if ($date !== false) {
                return $date;
            }
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Exception $e) {
            throw new InvalidDateException(sprintf('Cannot flexibly parse date "%s".', $value), 0, $e);
        }
    }

    public static function isValid(string $value, string $format = 'Y-m-d'): bool
    {
        $date = DateTimeImmutable::createFromFormat($format, $value);

        return $date !== false && $date->format($format) === $value;
    }

    public static function isValidIso8601(string $value): bool
    {
        return (bool) preg_match(
            '~^\d{4}-\d{2}-\d{2}(T\d{2}:\d{2}(:\d{2}(\.\d+)?)?(Z|[+-]\d{2}:?\d{2})?)?$~',
            $value
        );
    }

    public static function isPast(DateTimeImmutable $date, ?DateTimeImmutable $now = null): bool
    {
        return $date < ($now ?? new DateTimeImmutable);
    }

    public static function isFuture(DateTimeImmutable $date, ?DateTimeImmutable $now = null): bool
    {
        return $date > ($now ?? new DateTimeImmutable);
    }

    public static function isToday(DateTimeImmutable $date, ?DateTimeImmutable $now = null): bool
    {
        return self::isSameDay($date, $now ?? new DateTimeImmutable);
    }

    public static function isSameDay(DateTimeImmutable $a, DateTimeImmutable $b): bool
    {
        return $a->format('Y-m-d') === $b->format('Y-m-d');
    }

    public static function isSameWeek(DateTimeImmutable $a, DateTimeImmutable $b): bool
    {
        return $a->format('o-W') === $b->format('o-W');
    }

    public static function isSameMonth(DateTimeImmutable $a, DateTimeImmutable $b): bool
    {
        return $a->format('Y-m') === $b->format('Y-m');
    }

    public static function isInCurrentWeek(DateTimeImmutable $date, ?DateTimeImmutable $now = null): bool
    {
        return self::isSameWeek($date, $now ?? new DateTimeImmutable);
    }

    public static function isInCurrentMonth(DateTimeImmutable $date, ?DateTimeImmutable $now = null): bool
    {
        return self::isSameMonth($date, $now ?? new DateTimeImmutable);
    }

    public static function isWeekend(DateTimeImmutable $date): bool
    {
        $dow = (int) $date->format('N');

        return $dow >= 6;
    }

    public static function isWeekday(DateTimeImmutable $date): bool
    {
        return !self::isWeekend($date);
    }

    public static function startOfDay(DateTimeImmutable $date): DateTimeImmutable
    {
        return $date->setTime(0, 0);
    }

    public static function endOfDay(DateTimeImmutable $date): DateTimeImmutable
    {
        return $date->setTime(23, 59, 59, 999_999);
    }

    public static function startOfWeek(DateTimeImmutable $date, string $weekStart = 'monday'): DateTimeImmutable
    {
        $weekStart = mb_strtolower($weekStart);
        $dow = (int) $date->format('N'); // 1=Mon, 7=Sun
        $offset = $weekStart === 'sunday' ? ($dow % 7) : ($dow - 1);

        return self::startOfDay($date)->modify(sprintf('-%d days', $offset));
    }

    public static function endOfWeek(DateTimeImmutable $date, string $weekStart = 'monday'): DateTimeImmutable
    {
        return self::endOfDay(self::startOfWeek($date, $weekStart)->modify('+6 days'));
    }

    public static function startOfMonth(DateTimeImmutable $date): DateTimeImmutable
    {
        return self::startOfDay($date->modify('first day of this month'));
    }

    public static function endOfMonth(DateTimeImmutable $date): DateTimeImmutable
    {
        return self::endOfDay($date->modify('last day of this month'));
    }

    public static function startOfYear(DateTimeImmutable $date): DateTimeImmutable
    {
        return self::startOfDay($date->setDate((int) $date->format('Y'), 1, 1));
    }

    public static function endOfYear(DateTimeImmutable $date): DateTimeImmutable
    {
        return self::endOfDay($date->setDate((int) $date->format('Y'), 12, 31));
    }

    public static function nextWeek(DateTimeImmutable $date): DateTimeImmutable
    {
        return $date->modify('+1 week');
    }

    public static function previousWeek(DateTimeImmutable $date): DateTimeImmutable
    {
        return $date->modify('-1 week');
    }

    public static function nextMonth(DateTimeImmutable $date): DateTimeImmutable
    {
        return $date->modify('+1 month');
    }

    public static function previousMonth(DateTimeImmutable $date): DateTimeImmutable
    {
        return $date->modify('-1 month');
    }

    public static function diffInSeconds(DateTimeImmutable $a, DateTimeImmutable $b): int
    {
        return $b->getTimestamp() - $a->getTimestamp();
    }

    public static function diffInMinutes(DateTimeImmutable $a, DateTimeImmutable $b): int
    {
        return intdiv(self::diffInSeconds($a, $b), 60);
    }

    public static function diffInHours(DateTimeImmutable $a, DateTimeImmutable $b): int
    {
        return intdiv(self::diffInSeconds($a, $b), 3_600);
    }

    public static function diffInDays(DateTimeImmutable $a, DateTimeImmutable $b): int
    {
        $diff = self::startOfDay($a)->diff(self::startOfDay($b));

        return ($diff->invert === 1 ? -1 : 1) * $diff->days;
    }

    public static function diffInWeeks(DateTimeImmutable $a, DateTimeImmutable $b): int
    {
        return intdiv(self::diffInDays($a, $b), 7);
    }

    public static function diffInMonths(DateTimeImmutable $a, DateTimeImmutable $b): int
    {
        $diff = $a->diff($b);
        $months = ($diff->y * 12) + $diff->m;

        return $diff->invert === 1 ? -$months : $months;
    }

    public static function diffInYears(DateTimeImmutable $a, DateTimeImmutable $b): int
    {
        $diff = $a->diff($b);

        return $diff->invert === 1 ? -$diff->y : $diff->y;
    }

    public static function calculateAge(DateTimeImmutable $birthday, ?DateTimeImmutable $now = null): int
    {
        $now ??= new DateTimeImmutable;

        return $birthday->diff($now)->y;
    }

    public static function getQuarter(DateTimeImmutable $date): int
    {
        return (int) ceil((int) $date->format('n') / 3);
    }

    public static function getDayOfWeek(DateTimeImmutable $date): int
    {
        return (int) $date->format('N');
    }

    public static function formatIso(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d\TH:i:sP');
    }

    public static function formatHumanDate(DateTimeImmutable $date, string $locale = 'en'): string
    {
        if (class_exists(IntlDateFormatter::class)) {
            $formatter = new IntlDateFormatter(
                $locale,
                IntlDateFormatter::LONG,
                IntlDateFormatter::NONE
            );
            $result = $formatter->format($date);
            if ($result !== false) {
                return $result;
            }
        }

        return $date->format('F j, Y');
    }

    public static function formatRelative(DateTimeImmutable $date, ?DateTimeImmutable $now = null): string
    {
        $now ??= new DateTimeImmutable;
        $diff = self::diffInSeconds($date, $now);
        $abs = abs($diff);
        $isPast = $diff > 0;

        $value = match (true) {
            $abs < 60 => [$abs, 'second'],
            $abs < 3_600 => [intdiv($abs, 60), 'minute'],
            $abs < 86_400 => [intdiv($abs, 3_600), 'hour'],
            $abs < 2_592_000 => [intdiv($abs, 86_400), 'day'],
            $abs < 31_536_000 => [intdiv($abs, 2_592_000), 'month'],
            default => [intdiv($abs, 31_536_000), 'year'],
        };

        [$count, $unit] = $value;
        $plural = $count === 1 ? $unit : $unit . 's';

        return $isPast ? "{$count} {$plural} ago" : "in {$count} {$plural}";
    }
}
