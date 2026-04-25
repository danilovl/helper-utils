<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Array;

use InvalidArgumentException;

final class ArrayHelper
{
    /**
     * @param array<int|string, mixed> $array
     * @param list<int|string> $keys
     * @return array<int|string, mixed>
     */
    public static function only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * @param array<int|string, mixed> $array
     * @param list<int|string> $keys
     * @return array<int|string, mixed>
     */
    public static function except(array $array, array $keys): array
    {
        return array_diff_key($array, array_flip($keys));
    }

    /**
     * @param array<int|string, mixed> $array
     */
    public static function isAssociative(array $array): bool
    {
        if ($array === []) {
            return false;
        }

        return !array_is_list($array);
    }

    /**
     * @param array<int|string, mixed> $array
     */
    public static function isList(array $array): bool
    {
        return array_is_list($array);
    }

    /**
     * @param array<int|string, mixed> $array
     * @return array<int|string, mixed>
     */
    public static function flatten(array $array, int $depth = PHP_INT_MAX): array
    {
        $result = [];
        foreach ($array as $item) {
            if (is_array($item) && $depth > 0) {
                foreach (self::flatten($item, $depth - 1) as $subItem) {
                    $result[] = $subItem;
                }
            } else {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @param array<int|string, mixed> $array
     * @return array<string, mixed>
     */
    public static function flattenWithDots(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $newKey = $prefix === '' ? (string) $key : $prefix . '.' . $key;
            if (is_array($value) && $value !== []) {
                $result = array_merge($result, self::flattenWithDots($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    /**
     * @param array<int|string, mixed> $array
     */
    public static function get(array $array, string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        $current = $array;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return $default;
            }
            $current = $current[$segment];
        }

        return $current;
    }

    /**
     * @param array<int|string, mixed> $array
     */
    public static function set(array &$array, string $key, mixed $value): void
    {
        $segments = explode('.', $key);
        $current = &$array;
        foreach ($segments as $segment) {
            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                $current[$segment] = [];
            }
            $current = &$current[$segment];
        }
        $current = $value;
    }

    /**
     * @param array<int|string, mixed> $array
     */
    public static function has(array $array, string $key): bool
    {
        if (array_key_exists($key, $array)) {
            return true;
        }

        $current = $array;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return false;
            }
            $current = $current[$segment];
        }

        return true;
    }

    /**
     * @param array<int|string, mixed> $array
     */
    public static function forget(array &$array, string $key): void
    {
        $segments = explode('.', $key);
        $last = array_pop($segments);
        $current = &$array;

        foreach ($segments as $segment) {
            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                return;
            }
            $current = &$current[$segment];
        }

        unset($current[$last]);
    }

    /**
     * @param array<int|string, mixed> ...$arrays
     * @return array<int|string, mixed>
     */
    public static function recursiveMerge(array ...$arrays): array
    {
        $result = [];
        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (is_int($key)) {
                    $result[] = $value;
                } elseif (is_array($value) && isset($result[$key]) && is_array($result[$key])) {
                    $result[$key] = self::recursiveMerge($result[$key], $value);
                } else {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * @param array<int|string, mixed> $a
     * @param array<int|string, mixed> $b
     * @return array<int|string, mixed>
     */
    public static function recursiveDiff(array $a, array $b): array
    {
        $result = [];
        foreach ($a as $key => $value) {
            if (!array_key_exists($key, $b)) {
                $result[$key] = $value;

                continue;
            }
            if (is_array($value) && is_array($b[$key])) {
                $diff = self::recursiveDiff($value, $b[$key]);
                if ($diff !== []) {
                    $result[$key] = $diff;
                }
            } elseif ($value !== $b[$key]) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * @template T
     * @param array<int|string, T> $array
     * @param callable(T, int|string): bool $predicate
     * @return array{0: array<int|string, T>, 1: array<int|string, T>}
     */
    public static function partition(array $array, callable $predicate): array
    {
        $matches = [];
        $rest = [];
        foreach ($array as $key => $value) {
            if ($predicate($value, $key)) {
                $matches[$key] = $value;
            } else {
                $rest[$key] = $value;
            }
        }

        return [$matches, $rest];
    }

    /**
     * @param array<int|string, mixed> $array
     * @return list<array<int|string, mixed>>
     */
    public static function chunk(array $array, int $size, bool $preserveKeys = false): array
    {
        if ($size < 1) {
            throw new InvalidArgumentException('Chunk size must be at least 1.');
        }

        return array_chunk($array, $size, $preserveKeys);
    }

    /**
     * @template T
     * @param array<int|string, T> $array
     * @return array<int|string, T>
     */
    public static function shuffle(array $array): array
    {
        $keys = array_keys($array);
        shuffle($keys);
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $array[$key];
        }

        return $result;
    }

    /**
     * @param array<int|string, mixed> $array
     * @return list<mixed>
     */
    public static function pluck(array $array, string $key): array
    {
        $result = [];
        foreach ($array as $item) {
            if (is_array($item) && array_key_exists($key, $item)) {
                $result[] = $item[$key];
            } elseif (is_object($item) && isset($item->{$key})) {
                $result[] = $item->{$key};
            }
        }

        return $result;
    }

    /**
     * @template T
     * @param array<int|string, T> $array
     * @return array<int|string, T>
     */
    public static function unique(array $array): array
    {
        return array_values(array_unique($array, SORT_REGULAR));
    }

    /**
     * @template T
     * @param array<int|string, T> $array
     * @param callable(T, int|string): bool $predicate
     * @return T|null
     */
    public static function findFirst(array $array, callable $predicate): mixed
    {
        return array_find($array, static fn ($value, $key) => $predicate($value, $key));

    }

    /**
     * @template T
     * @param array<int|string, T> $array
     * @param callable(T, int|string): bool $predicate
     * @return T|null
     */
    public static function findLast(array $array, callable $predicate): mixed
    {
        $found = null;
        foreach ($array as $key => $value) {
            if ($predicate($value, $key)) {
                $found = $value;
            }
        }

        return $found;
    }
}
