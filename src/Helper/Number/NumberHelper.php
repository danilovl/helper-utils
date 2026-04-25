<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Number;

use NumberFormatter;
use InvalidArgumentException;

final class NumberHelper
{
    public static function clamp(int|float $value, int|float $min, int|float $max): int|float
    {
        if ($min > $max) {
            throw new InvalidArgumentException('Min cannot be greater than max.');
        }

        return max($min, min($max, $value));
    }

    public static function lerp(float $a, float $b, float $t): float
    {
        return $a + ($b - $a) * $t;
    }

    public static function round(float $value, int $precision = 0, int $mode = PHP_ROUND_HALF_UP): float
    {
        /** @var 1|2|3|4 $mode */
        return round($value, $precision, $mode);
    }

    public static function floor(float $value, int $precision = 0): float
    {
        $multiplier = 10 ** $precision;

        return floor($value * $multiplier) / $multiplier;
    }

    public static function ceil(float $value, int $precision = 0): float
    {
        $multiplier = 10 ** $precision;

        return ceil($value * $multiplier) / $multiplier;
    }

    public static function format(int|float $value, int $decimals = 0, string $locale = 'en'): string
    {
        if (class_exists(NumberFormatter::class)) {
            $formatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimals);
            $result = $formatter->format($value);
            if (is_string($result)) {
                return $result;
            }
        }

        return number_format((float) $value, $decimals);
    }

    public static function formatPercent(float $value, int $decimals = 0): string
    {
        return number_format($value * 100, $decimals) . '%';
    }

    public static function formatOrdinal(int $value, string $locale = 'en'): string
    {
        if (class_exists(NumberFormatter::class)) {
            $formatter = new NumberFormatter($locale, NumberFormatter::ORDINAL);
            $result = $formatter->format($value);
            if (is_string($result)) {
                return $result;
            }
        }

        $mod100 = $value % 100;
        if ($mod100 >= 11 && $mod100 <= 13) {
            return $value . 'th';
        }
        $suffix = match ($value % 10) {
            1 => 'st',
            2 => 'nd',
            3 => 'rd',
            default => 'th',
        };

        return $value . $suffix;
    }

    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $sign = $bytes < 0 ? '-' : '';
        $bytes = abs($bytes);
        $unitIndex = 0;
        $value = (float) $bytes;
        while ($value >= 1_024 && $unitIndex < count($units) - 1) {
            $value /= 1_024;
            $unitIndex++;
        }

        return $sign . number_format($value, $precision) . ' ' . $units[$unitIndex];
    }

    public static function isEven(int $value): bool
    {
        return $value % 2 === 0;
    }

    public static function isOdd(int $value): bool
    {
        return $value % 2 !== 0;
    }

    public static function isPrime(int $value): bool
    {
        if ($value < 2) {
            return false;
        }
        if ($value < 4) {
            return true;
        }
        if ($value % 2 === 0) {
            return false;
        }
        for ($i = 3, $max = (int) sqrt($value); $i <= $max; $i += 2) {
            if ($value % $i === 0) {
                return false;
            }
        }

        return true;
    }

    public static function gcd(int $a, int $b): int
    {
        $a = abs($a);
        $b = abs($b);
        while ($b !== 0) {
            [$a, $b] = [$b, $a % $b];
        }

        return $a;
    }

    public static function lcm(int $a, int $b): int
    {
        if ($a === 0 || $b === 0) {
            return 0;
        }

        return (int) (abs($a * $b) / self::gcd($a, $b));
    }

    public static function randomFloat(float $min = 0.0, float $max = 1.0): float
    {
        return $min + ($max - $min) * (random_int(0, PHP_INT_MAX) / PHP_INT_MAX);
    }

    public static function randomInt(int $min, int $max): int
    {
        return random_int($min, $max);
    }
}
