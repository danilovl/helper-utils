<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\File;

use Danilovl\HelperUtils\Exception\HelperException;
use Symfony\Component\Filesystem\Filesystem;
use InvalidArgumentException;

final class FileHelper
{
    public static function createTmpFile(?string $content = null, string $extension = '', string $prefix = 'tmp_'): string
    {
        $tmpDir = sys_get_temp_dir();
        $path = tempnam($tmpDir, $prefix);
        if ($path === false) {
            throw new HelperException('Failed to create temporary file.');
        }

        if ($extension !== '') {
            $newPath = $path . '.' . mb_ltrim($extension, '.');
            if (!rename($path, $newPath)) {
                @unlink($path);

                throw new HelperException('Failed to rename temporary file with extension.');
            }
            $path = $newPath;
        }

        if ($content !== null && file_put_contents($path, $content) === false) {
            @unlink($path);

            throw new HelperException(sprintf('Failed to write content to "%s".', $path));
        }

        return $path;
    }

    public static function deleteDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        (new Filesystem)->remove($path);
    }

    public static function ensureDirectory(string $path, int $mode = 0o755): void
    {
        (new Filesystem)->mkdir($path, $mode);
    }

    public static function copyDirectory(string $source, string $destination): void
    {
        if (!is_dir($source)) {
            throw new HelperException(sprintf('Source directory "%s" does not exist.', $source));
        }

        (new Filesystem)->mirror($source, $destination);
    }

    public static function getExtension(string $filename): string
    {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    public static function getBasenameWithoutExtension(string $filename): string
    {
        return pathinfo($filename, PATHINFO_FILENAME);
    }

    public static function sanitizeFilename(string $filename): string
    {
        $filename = preg_replace('~[\x00-\x1F\x7F]~u', '', $filename) ?? $filename;
        $filename = preg_replace('~[/\\\\:\*\?"<>\|]~u', '_', $filename) ?? $filename;
        $filename = preg_replace('~^[\.\s]+|[\.\s]+$~u', '', $filename) ?? $filename;
        $filename = preg_replace('~_+~', '_', $filename) ?? $filename;

        if ($filename === '' || $filename === '.' || $filename === '..') {
            return 'file';
        }

        return $filename;
    }

    public static function generateUniqueFilename(string $directory, string $filename): string
    {
        $directory = mb_rtrim($directory, '/\\');
        $base = self::getBasenameWithoutExtension($filename);
        $ext = self::getExtension($filename);
        $extPart = $ext !== '' ? '.' . $ext : '';

        $candidate = $filename;
        $counter = 1;
        while (file_exists($directory . DIRECTORY_SEPARATOR . $candidate)) {
            $candidate = $base . '_' . $counter . $extPart;
            $counter++;
        }

        return $candidate;
    }

    public static function humanReadableSize(int $bytes, int $precision = 2): string
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

    public static function parseSize(string $size): int
    {
        $size = mb_trim($size);
        if (!preg_match('~^([\d.]+)\s*([KMGTP]?B?)$~i', $size, $matches)) {
            throw new InvalidArgumentException(sprintf('Invalid size format: "%s".', $size));
        }
        $value = (float) $matches[1];
        $unit = mb_strtoupper($matches[2]);
        $multiplier = match ($unit) {
            '', 'B' => 1,
            'K', 'KB' => 1_024,
            'M', 'MB' => 1_024 ** 2,
            'G', 'GB' => 1_024 ** 3,
            'T', 'TB' => 1_024 ** 4,
            'P', 'PB' => 1_024 ** 5,
            default => throw new InvalidArgumentException(sprintf('Unknown size unit: "%s".', $unit)),
        };

        return (int) ($value * $multiplier);
    }

    public static function isWritable(string $path): bool
    {
        return is_writable($path);
    }

    public static function isHidden(string $path): bool
    {
        $basename = basename($path);

        return $basename !== '' && $basename[0] === '.';
    }

    public static function fileHash(string $path, string $algorithm = 'sha256'): string
    {
        if (!is_file($path)) {
            throw new HelperException(sprintf('File "%s" does not exist.', $path));
        }

        $hash = @hash_file($algorithm, $path);
        if ($hash === false) {
            throw new HelperException(sprintf('Failed to hash file "%s".', $path));
        }

        return $hash;
    }
}
