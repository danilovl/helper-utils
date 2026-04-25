<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Color;

use Danilovl\HelperUtils\Exception\HelperException;
use Danilovl\HelperUtils\Helper\Color\ColorHelper;
use PHPUnit\Framework\TestCase;

final class ColorHelperTest extends TestCase
{
    public function testHexToRgb(): void
    {
        self::assertSame(['r' => 255, 'g' => 0, 'b' => 0], ColorHelper::hexToRgb('#ff0000'));
        self::assertSame(['r' => 0, 'g' => 255, 'b' => 0], ColorHelper::hexToRgb('00ff00'));
    }

    public function testHexToRgbShort(): void
    {
        self::assertSame(['r' => 255, 'g' => 0, 'b' => 0], ColorHelper::hexToRgb('#f00'));
    }

    public function testHexToRgbInvalid(): void
    {
        $this->expectException(HelperException::class);
        ColorHelper::hexToRgb('not a color');
    }

    public function testRgbToHex(): void
    {
        self::assertSame('#ff0000', ColorHelper::rgbToHex(255, 0, 0));
        self::assertSame('#000000', ColorHelper::rgbToHex(0, 0, 0));
    }

    public function testRgbToHexInvalid(): void
    {
        $this->expectException(HelperException::class);
        ColorHelper::rgbToHex(256, 0, 0);
    }

    public function testLighten(): void
    {
        self::assertSame('#ffffff', ColorHelper::lighten('#000000', 1.0));
        self::assertSame('#000000', ColorHelper::lighten('#000000', 0.0));
        self::assertSame('#808080', ColorHelper::lighten('#000000', 0.5));
    }

    public function testDarken(): void
    {
        self::assertSame('#000000', ColorHelper::darken('#ffffff', 1.0));
        self::assertSame('#ffffff', ColorHelper::darken('#ffffff', 0.0));
    }

    public function testComplementary(): void
    {
        self::assertSame('#00ffff', ColorHelper::complementary('#ff0000'));
        self::assertSame('#ff00ff', ColorHelper::complementary('#00ff00'));
    }

    public function testGetContrastTextColor(): void
    {
        self::assertSame('#000000', ColorHelper::getContrastTextColor('#ffffff'));
        self::assertSame('#ffffff', ColorHelper::getContrastTextColor('#000000'));
    }

    public function testContrastRatio(): void
    {
        self::assertEqualsWithDelta(21.0, ColorHelper::contrastRatio('#000000', '#ffffff'), 0.01);
        self::assertEqualsWithDelta(1.0, ColorHelper::contrastRatio('#ffffff', '#ffffff'), 0.01);
    }

    public function testMeetsWcagAA(): void
    {
        self::assertTrue(ColorHelper::meetsWcagAA('#000000', '#ffffff'));
        self::assertFalse(ColorHelper::meetsWcagAA('#cccccc', '#ffffff'));
    }

    public function testIsValidHex(): void
    {
        self::assertTrue(ColorHelper::isValidHex('#ff0000'));
        self::assertTrue(ColorHelper::isValidHex('ff0000'));
        self::assertTrue(ColorHelper::isValidHex('#f00'));
        self::assertFalse(ColorHelper::isValidHex('not a color'));
        self::assertFalse(ColorHelper::isValidHex('#ggg'));
    }

    public function testRandomHex(): void
    {
        $hex = ColorHelper::randomHex();
        self::assertMatchesRegularExpression('/^#[a-f0-9]{6}$/', $hex);
    }
}
