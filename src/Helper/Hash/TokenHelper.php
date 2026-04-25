<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Hash;

use Symfony\Component\Uid\{Ulid, Uuid};
use InvalidArgumentException;

final class TokenHelper
{
    /**
     * @param int<1, max> $length
     */
    public static function generate(int $length = 32): string
    {
        /** @phpstan-ignore smaller.alwaysFalse */
        if ($length < 1) {
            throw new InvalidArgumentException('Length must be at least 1.');
        }
        $bytes = random_bytes(max(1, (int) ceil($length / 2)));

        return mb_substr(bin2hex($bytes), 0, $length);
    }

    /**
     * @param int<1, max> $length
     */
    public static function generateUrlSafe(int $length = 32): string
    {
        /** @phpstan-ignore smaller.alwaysFalse */
        if ($length < 1) {
            throw new InvalidArgumentException('Length must be at least 1.');
        }
        $bytes = random_bytes($length);
        $encoded = $bytes
                |> base64_encode(...)
                |> (static fn ($x) => strtr($x, '+/', '-_'))
                |> (static fn ($x) => mb_rtrim($x, '='));

        return mb_substr($encoded, 0, $length);
    }

    /**
     * @param int<1, max> $length
     */
    public static function generateNumericCode(int $length = 6): string
    {
        /** @phpstan-ignore smaller.alwaysFalse */
        if ($length < 1) {
            throw new InvalidArgumentException('Length must be at least 1.');
        }
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= random_int(0, 9);
        }

        return $code;
    }

    public static function uuid(): string
    {
        return Uuid::v4()->toRfc4122();
    }

    public static function uuidV7(): string
    {
        return Uuid::v7()->toRfc4122();
    }

    public static function ulid(): string
    {
        return (string) new Ulid;
    }
}
