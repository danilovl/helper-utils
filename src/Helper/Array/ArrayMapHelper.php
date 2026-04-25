<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Array;

use Error;

final class ArrayMapHelper
{
    /**
     * @param iterable<mixed> $items
     * @return list<mixed>
     */
    public static function extractField(iterable $items, string $field): array
    {
        $result = [];
        foreach ($items as $item) {
            $result[] = self::resolveValue($item, $field);
        }

        return $result;
    }

    /**
     * @param iterable<mixed> $items
     * @param string|callable(mixed): (int|string) $key
     * @return array<int|string, list<mixed>>
     */
    public static function groupBy(iterable $items, string|callable $key): array
    {
        $result = [];
        foreach ($items as $item) {
            $groupKey = is_callable($key) ? $key($item) : self::resolveValue($item, $key);
            if (!is_int($groupKey) && !is_string($groupKey)) {
                $groupKey = (string) $groupKey;
            }
            $result[$groupKey][] = $item;
        }

        return $result;
    }

    /**
     * @param iterable<mixed> $items
     * @param string|callable(mixed): (int|string) $key
     * @return array<int|string, mixed>
     */
    public static function indexBy(iterable $items, string|callable $key): array
    {
        $result = [];
        foreach ($items as $item) {
            $idx = is_callable($key) ? $key($item) : self::resolveValue($item, $key);
            if (!is_int($idx) && !is_string($idx)) {
                $idx = (string) $idx;
            }
            $result[$idx] = $item;
        }

        return $result;
    }

    /**
     * @param iterable<mixed> $items
     * @param string|callable(mixed): (int|float) $field
     */
    public static function sumBy(iterable $items, string|callable $field): float|int
    {
        $sum = 0;
        $isFloat = false;
        foreach ($items as $item) {
            $value = is_callable($field) ? $field($item) : self::resolveValue($item, $field);
            if (is_float($value)) {
                $isFloat = true;
            }
            $sum += (float) $value;
        }

        return $isFloat ? $sum : (int) $sum;
    }

    /**
     * @param iterable<mixed> $items
     * @param string|callable(mixed): (int|float) $field
     */
    public static function avgBy(iterable $items, string|callable $field): float
    {
        $sum = 0.0;
        $count = 0;
        foreach ($items as $item) {
            $value = is_callable($field) ? $field($item) : self::resolveValue($item, $field);
            $sum += (float) $value;
            $count++;
        }

        return $count === 0 ? 0.0 : $sum / $count;
    }

    /**
     * @param iterable<mixed> $items
     * @param string|callable(mixed): mixed $field
     */
    public static function maxBy(iterable $items, string|callable $field): mixed
    {
        $max = null;
        $maxValue = null;
        foreach ($items as $item) {
            $value = is_callable($field) ? $field($item) : self::resolveValue($item, $field);
            if ($maxValue === null || $value > $maxValue) {
                $maxValue = $value;
                $max = $item;
            }
        }

        return $max;
    }

    /**
     * @param iterable<mixed> $items
     * @param string|callable(mixed): mixed $field
     */
    public static function minBy(iterable $items, string|callable $field): mixed
    {
        $min = null;
        $minValue = null;
        foreach ($items as $item) {
            $value = is_callable($field) ? $field($item) : self::resolveValue($item, $field);
            if ($minValue === null || $value < $minValue) {
                $minValue = $value;
                $min = $item;
            }
        }

        return $min;
    }

    /**
     * @param iterable<mixed> $items
     * @param string|callable(mixed): (int|string) $field
     * @return array<int|string, int>
     */
    public static function countBy(iterable $items, string|callable $field): array
    {
        $result = [];
        foreach ($items as $item) {
            $key = is_callable($field) ? $field($item) : self::resolveValue($item, $field);
            if (!is_int($key) && !is_string($key)) {
                $key = (string) $key;
            }
            $result[$key] = ($result[$key] ?? 0) + 1;
        }

        return $result;
    }

    private static function resolveValue(mixed $item, string $field): mixed
    {
        if (is_array($item)) {
            return $item[$field] ?? null;
        }

        if (!is_object($item)) {
            return null;
        }

        if (isset($item->{$field}) || property_exists($item, $field)) {
            try {
                return $item->{$field};
            } catch (Error) {
                // not accessible, fall through to getters
            }
        }

        $getters = ['get' . ucfirst($field), 'is' . ucfirst($field), 'has' . ucfirst($field), $field];
        foreach ($getters as $method) {
            if (method_exists($item, $method)) {
                return $item->{$method}();
            }
        }

        return null;
    }
}
