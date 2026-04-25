<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Compare;

use Danilovl\HelperUtils\Exception\HelperException;

final class VersionHelper
{
    public static function compare(string $a, string $b): int
    {
        return version_compare($a, $b);
    }

    public static function isGreater(string $a, string $b): bool
    {
        return version_compare($a, $b, '>');
    }

    /**
     * Supports caret (^), tilde (~), comparison operators and exact match.
     * Examples: "^1.0", "~1.2", ">=1.0", "1.5.0"
     */
    public static function satisfies(string $version, string $constraint): bool
    {
        $constraint = mb_trim($constraint);

        if ($constraint === '') {
            return false;
        }

        if (str_starts_with($constraint, '^')) {
            $base = mb_substr($constraint, 1);
            $major = self::major($base);
            $upper = ($major + 1) . '.0.0';

            return version_compare($version, $base, '>=') && version_compare($version, $upper, '<');
        }

        if (str_starts_with($constraint, '~')) {
            $base = mb_substr($constraint, 1);
            $parts = explode('.', $base);
            if (count($parts) >= 2) {
                $upperMajor = (int) $parts[0];
                $upperMinor = (int) $parts[1] + 1;
                $upper = $upperMajor . '.' . $upperMinor . '.0';
            } else {
                $upper = ((int) $parts[0] + 1) . '.0.0';
            }

            return version_compare($version, $base, '>=') && version_compare($version, $upper, '<');
        }

        foreach (['>=', '<=', '!=', '==', '>', '<', '='] as $op) {
            if (str_starts_with($constraint, $op)) {
                $value = $op
                        |> mb_strlen(...)
                        |> (static fn ($x) => mb_substr($constraint, $x))
                        |> mb_trim(...);

                return version_compare($version, $value, $op === '=' ? '==' : $op);
            }
        }

        return version_compare($version, $constraint, '==');
    }

    public static function major(string $version): int
    {
        return self::part($version, 0);
    }

    public static function minor(string $version): int
    {
        return self::part($version, 1);
    }

    public static function patch(string $version): int
    {
        return self::part($version, 2);
    }

    private static function part(string $version, int $index): int
    {
        $version = mb_ltrim($version, 'vV');
        $version = preg_replace('~[+\-].*$~', '', $version) ?? $version;
        $parts = explode('.', $version);

        if (!isset($parts[$index])) {
            return 0;
        }

        if (!ctype_digit($parts[$index])) {
            throw new HelperException(sprintf('Invalid version part "%s" in "%s".', $parts[$index], $version));
        }

        return (int) $parts[$index];
    }
}
