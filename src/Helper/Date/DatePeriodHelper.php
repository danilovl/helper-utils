<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Date;

use DateInterval;
use DateTimeImmutable;
use Generator;

final class DatePeriodHelper
{
    /**
     * @return Generator<DateTimeImmutable>
     */
    public static function daysBetween(DateTimeImmutable $start, DateTimeImmutable $end): Generator
    {
        return self::iterate(DateHelper::startOfDay($start), DateHelper::startOfDay($end), 'P1D');
    }

    /**
     * @return Generator<DateTimeImmutable>
     */
    public static function weeksBetween(DateTimeImmutable $start, DateTimeImmutable $end): Generator
    {
        return self::iterate($start, $end, 'P1W');
    }

    /**
     * @return Generator<DateTimeImmutable>
     */
    public static function monthsBetween(DateTimeImmutable $start, DateTimeImmutable $end): Generator
    {
        return self::iterate($start, $end, 'P1M');
    }

    public static function intersect(
        DateTimeImmutable $start1,
        DateTimeImmutable $end1,
        DateTimeImmutable $start2,
        DateTimeImmutable $end2
    ): bool {
        return $start1 <= $end2 && $start2 <= $end1;
    }

    /**
     * @return array{0: DateTimeImmutable, 1: DateTimeImmutable}|null
     */
    public static function overlap(
        DateTimeImmutable $start1,
        DateTimeImmutable $end1,
        DateTimeImmutable $start2,
        DateTimeImmutable $end2
    ): ?array {
        if (!self::intersect($start1, $end1, $start2, $end2)) {
            return null;
        }

        return [
            max($start1, $start2),
            min($end1, $end2),
        ];
    }

    /**
     * @return Generator<DateTimeImmutable>
     */
    private static function iterate(DateTimeImmutable $start, DateTimeImmutable $end, string $intervalSpec): Generator
    {
        $interval = new DateInterval($intervalSpec);
        $current = $start;

        while ($current <= $end) {
            yield $current;
            $current = $current->add($interval);
        }
    }
}
