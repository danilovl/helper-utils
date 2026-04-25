<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Compare;

use Danilovl\HelperUtils\Enum\ComparisonOperator;

final class CompareHelper
{
    public static function compare(mixed $a, ComparisonOperator $op, mixed $b): bool
    {
        return match ($op) {
            ComparisonOperator::EQUAL => $a == $b,
            ComparisonOperator::STRICT_EQUAL => $a === $b,
            ComparisonOperator::NOT_EQUAL => $a != $b,
            ComparisonOperator::STRICT_NOT_EQUAL => $a !== $b,
            ComparisonOperator::GREATER_THAN => $a > $b,
            ComparisonOperator::GREATER_OR_EQUAL => $a >= $b,
            ComparisonOperator::LESS_THAN => $a < $b,
            ComparisonOperator::LESS_OR_EQUAL => $a <= $b,
            ComparisonOperator::BETWEEN => self::compareBetween($a, $b),
            ComparisonOperator::IN => is_array($b) && in_array($a, $b, true),
            ComparisonOperator::NOT_IN => is_array($b) && !in_array($a, $b, true),
            ComparisonOperator::CONTAINS => is_string($a) && is_string($b) && str_contains($a, $b),
            ComparisonOperator::STARTS_WITH => is_string($a) && is_string($b) && str_starts_with($a, $b),
            ComparisonOperator::ENDS_WITH => is_string($a) && is_string($b) && str_ends_with($a, $b),
            ComparisonOperator::MATCHES_REGEX => is_string($a) && is_string($b) && preg_match($b, $a) === 1,
        };
    }

    public static function between(int|float $value, int|float $min, int|float $max, bool $inclusive = true): bool
    {
        return $inclusive
            ? ($value >= $min && $value <= $max)
            : ($value > $min && $value < $max);
    }

    /**
     * @param array<int|string, mixed> $candidates
     */
    public static function equalsAny(mixed $value, array $candidates, bool $strict = true): bool
    {
        return in_array($value, $candidates, $strict);
    }

    public static function spaceshipDeep(mixed $a, mixed $b): int
    {
        if (is_array($a) && is_array($b)) {
            $countCmp = count($a) <=> count($b);
            if ($countCmp !== 0) {
                return $countCmp;
            }
            foreach ($a as $key => $value) {
                if (!array_key_exists($key, $b)) {
                    return 1;
                }
                $cmp = self::spaceshipDeep($value, $b[$key]);
                if ($cmp !== 0) {
                    return $cmp;
                }
            }

            return 0;
        }

        return $a <=> $b;
    }

    private static function compareBetween(mixed $a, mixed $b): bool
    {
        if (!is_array($b) || count($b) !== 2 || !(is_int($a) || is_float($a))) {
            return false;
        }

        [$min, $max] = array_values($b);

        if (!(is_int($min) || is_float($min)) || !(is_int($max) || is_float($max))) {
            return false;
        }

        return self::between($a, $min, $max);
    }
}
