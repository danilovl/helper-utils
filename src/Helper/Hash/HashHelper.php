<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Hash;

use Danilovl\HelperUtils\Exception\HelperException;

final class HashHelper
{
    public static function md5(string $value): string
    {
        return md5($value);
    }

    public static function sha256(string $value): string
    {
        return hash('sha256', $value);
    }

    public static function sha512(string $value): string
    {
        return hash('sha512', $value);
    }

    public static function hash(string $value, string $algorithm = 'sha256'): string
    {
        if (!in_array($algorithm, hash_algos(), true)) {
            throw new HelperException(sprintf('Unknown hash algorithm "%s".', $algorithm));
        }

        return hash($algorithm, $value);
    }

    public static function fileHash(string $path, string $algorithm = 'sha256'): string
    {
        if (!is_file($path)) {
            throw new HelperException(sprintf('File "%s" does not exist.', $path));
        }

        $result = @hash_file($algorithm, $path);
        if ($result === false) {
            throw new HelperException(sprintf('Failed to hash file "%s".', $path));
        }

        return $result;
    }

    public static function safeEquals(string $a, string $b): bool
    {
        return hash_equals($a, $b);
    }
}
