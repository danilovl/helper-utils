<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\File;

use Symfony\Component\Filesystem\Path;

final class PathHelper
{
    public static function join(string ...$segments): string
    {
        $cleaned = [];
        foreach ($segments as $i => $segment) {
            $segment = str_replace('\\', '/', $segment);
            if ($i === 0) {
                $cleaned[] = mb_rtrim($segment, '/');
            } else {
                $cleaned[] = mb_trim($segment, '/');
            }
        }
        $result = implode('/', array_filter($cleaned, static fn (string $s): bool => $s !== ''));

        if (count($segments) > 0 && str_starts_with(str_replace('\\', '/', $segments[0]), '/')) {
            $result = '/' . mb_ltrim($result, '/');
        }

        return $result;
    }

    public static function normalize(string $path): string
    {
        return Path::canonicalize($path);
    }

    public static function isAbsolute(string $path): bool
    {
        return Path::isAbsolute($path);
    }

    public static function makeAbsolute(string $path, string $baseDir): string
    {
        return Path::makeAbsolute($path, $baseDir);
    }

    public static function makeRelative(string $path, string $baseDir): string
    {
        return Path::makeRelative($path, $baseDir);
    }

    /**
     * Path-traversal-safe check.
     * Both paths are normalized; result is true only if $path is inside $directory.
     */
    public static function isWithin(string $path, string $directory): bool
    {
        $normalizedPath = self::normalize($path);
        $normalizedDir = mb_rtrim(self::normalize($directory), '/');

        if ($normalizedDir === '') {
            return false;
        }

        return $normalizedPath === $normalizedDir || str_starts_with($normalizedPath, $normalizedDir . '/');
    }
}
