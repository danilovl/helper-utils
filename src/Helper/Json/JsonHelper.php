<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Json;

use JsonException;

final class JsonHelper
{
    /**
     * @throws JsonException
     */
    public static function decode(string $json, bool $assoc = true): mixed
    {
        return json_decode($json, $assoc, 512, JSON_THROW_ON_ERROR);
    }

    public static function tryDecode(string $json, bool $assoc = true): mixed
    {
        try {
            return self::decode($json, $assoc);
        } catch (JsonException) {
            return null;
        }
    }

    /**
     * @throws JsonException
     */
    public static function encode(mixed $value, int $flags = 0): string
    {
        return json_encode($value, JSON_THROW_ON_ERROR | $flags);
    }

    public static function tryEncode(mixed $value, int $flags = 0): ?string
    {
        try {
            return self::encode($value, $flags);
        } catch (JsonException) {
            return null;
        }
    }

    public static function pretty(mixed $value): string
    {
        return self::encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function isValid(string $json): bool
    {
        return self::tryDecode($json) !== null || mb_trim($json) === 'null';
    }

    /**
     * @throws JsonException
     */
    public static function deepMerge(string $a, string $b): string
    {
        $arrA = self::decode($a);
        $arrB = self::decode($b);
        if (!is_array($arrA) || !is_array($arrB)) {
            return self::encode($arrB);
        }

        return self::encode(self::mergeArrays($arrA, $arrB));
    }

    /**
     * @param array<int|string, mixed> $a
     * @param array<int|string, mixed> $b
     * @return array<int|string, mixed>
     */
    private static function mergeArrays(array $a, array $b): array
    {
        foreach ($b as $key => $value) {
            if (is_array($value) && isset($a[$key]) && is_array($a[$key])) {
                $a[$key] = self::mergeArrays($a[$key], $value);
            } else {
                $a[$key] = $value;
            }
        }

        return $a;
    }
}
