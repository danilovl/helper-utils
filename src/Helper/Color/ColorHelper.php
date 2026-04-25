<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Color;

use Danilovl\HelperUtils\Exception\HelperException;

final class ColorHelper
{
    /**
     * @return array{r: int, g: int, b: int}
     */
    public static function hexToRgb(string $hex): array
    {
        $hex = self::normalizeHex($hex);

        return [
            'r' => (int) hexdec(mb_substr($hex, 0, 2)),
            'g' => (int) hexdec(mb_substr($hex, 2, 2)),
            'b' => (int) hexdec(mb_substr($hex, 4, 2)),
        ];
    }

    public static function rgbToHex(int $r, int $g, int $b): string
    {
        foreach ([$r, $g, $b] as $component) {
            if ($component < 0 || $component > 255) {
                throw new HelperException(sprintf('RGB component "%d" out of range 0-255.', $component));
            }
        }

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    public static function lighten(string $hex, float $amount): string
    {
        $rgb = self::hexToRgb($hex);
        $amount = max(0.0, min(1.0, $amount));

        return self::rgbToHex(
            (int) round($rgb['r'] + (255 - $rgb['r']) * $amount),
            (int) round($rgb['g'] + (255 - $rgb['g']) * $amount),
            (int) round($rgb['b'] + (255 - $rgb['b']) * $amount)
        );
    }

    public static function darken(string $hex, float $amount): string
    {
        $rgb = self::hexToRgb($hex);
        $amount = max(0.0, min(1.0, $amount));
        $factor = 1.0 - $amount;

        return self::rgbToHex(
            (int) round($rgb['r'] * $factor),
            (int) round($rgb['g'] * $factor),
            (int) round($rgb['b'] * $factor)
        );
    }

    public static function complementary(string $hex): string
    {
        $rgb = self::hexToRgb($hex);

        return self::rgbToHex(255 - $rgb['r'], 255 - $rgb['g'], 255 - $rgb['b']);
    }

    public static function getContrastTextColor(string $backgroundHex): string
    {
        $rgb = self::hexToRgb($backgroundHex);
        $luminance = self::relativeLuminance($rgb['r'], $rgb['g'], $rgb['b']);

        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }

    public static function contrastRatio(string $hex1, string $hex2): float
    {
        $rgb1 = self::hexToRgb($hex1);
        $rgb2 = self::hexToRgb($hex2);
        $l1 = self::relativeLuminance($rgb1['r'], $rgb1['g'], $rgb1['b']);
        $l2 = self::relativeLuminance($rgb2['r'], $rgb2['g'], $rgb2['b']);
        $lighter = max($l1, $l2);
        $darker = min($l1, $l2);

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    public static function meetsWcagAA(string $foreground, string $background): bool
    {
        return self::contrastRatio($foreground, $background) >= 4.5;
    }

    public static function isValidHex(string $hex): bool
    {
        return preg_match('~^#?([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$~', $hex) === 1;
    }

    public static function randomHex(): string
    {
        return self::rgbToHex(random_int(0, 255), random_int(0, 255), random_int(0, 255));
    }

    private static function normalizeHex(string $hex): string
    {
        $hex = mb_ltrim(mb_strtolower($hex), '#');
        if (mb_strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (!preg_match('~^[a-f0-9]{6}$~', $hex)) {
            throw new HelperException(sprintf('Invalid hex color "%s".', $hex));
        }

        return $hex;
    }

    private static function relativeLuminance(int $r, int $g, int $b): float
    {
        $values = [];
        foreach ([$r, $g, $b] as $component) {
            $sRGB = $component / 255;
            $values[] = $sRGB <= 0.039_28
                ? $sRGB / 12.92
                : (($sRGB + 0.055) / 1.055) ** 2.4;
        }

        return 0.212_6 * $values[0] + 0.715_2 * $values[1] + 0.072_2 * $values[2];
    }
}
