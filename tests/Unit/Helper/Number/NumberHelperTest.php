<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Number;

use Danilovl\HelperUtils\Helper\Number\NumberHelper;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

final class NumberHelperTest extends TestCase
{
    public function testClamp(): void
    {
        self::assertSame(5, NumberHelper::clamp(5, 0, 10));
        self::assertSame(10, NumberHelper::clamp(15, 0, 10));
        self::assertSame(0, NumberHelper::clamp(-5, 0, 10));
    }

    public function testClampInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        NumberHelper::clamp(5, 10, 0);
    }

    public function testLerp(): void
    {
        self::assertSame(0.0, NumberHelper::lerp(0.0, 10.0, 0.0));
        self::assertSame(10.0, NumberHelper::lerp(0.0, 10.0, 1.0));
        self::assertSame(5.0, NumberHelper::lerp(0.0, 10.0, 0.5));
    }

    public function testRound(): void
    {
        self::assertSame(3.0, NumberHelper::round(2.5));
        self::assertSame(3.14, NumberHelper::round(3.141_59, 2));
    }

    public function testFloor(): void
    {
        self::assertSame(3.0, NumberHelper::floor(3.99));
        self::assertSame(3.14, NumberHelper::floor(3.149, 2));
    }

    public function testCeil(): void
    {
        self::assertSame(4.0, NumberHelper::ceil(3.01));
        self::assertSame(3.15, NumberHelper::ceil(3.141, 2));
    }

    public function testFormat(): void
    {
        self::assertSame('1,234', NumberHelper::format(1_234));
        self::assertSame('1,234.50', NumberHelper::format(1_234.5, 2));
    }

    public function testFormatPercent(): void
    {
        self::assertSame('50%', NumberHelper::formatPercent(0.5));
        self::assertSame('15.6%', NumberHelper::formatPercent(0.156, 1));
    }

    public function testFormatOrdinal(): void
    {
        self::assertSame('1st', NumberHelper::formatOrdinal(1));
        self::assertSame('2nd', NumberHelper::formatOrdinal(2));
        self::assertSame('3rd', NumberHelper::formatOrdinal(3));
        self::assertSame('4th', NumberHelper::formatOrdinal(4));
        self::assertSame('11th', NumberHelper::formatOrdinal(11));
        self::assertSame('21st', NumberHelper::formatOrdinal(21));
    }

    public function testFormatBytes(): void
    {
        self::assertSame('500.00 B', NumberHelper::formatBytes(500));
        self::assertSame('1.00 KB', NumberHelper::formatBytes(1_024));
        self::assertSame('1.50 MB', NumberHelper::formatBytes(1_572_864));
    }

    public function testEvenOdd(): void
    {
        self::assertTrue(NumberHelper::isEven(4));
        self::assertFalse(NumberHelper::isEven(5));
        self::assertTrue(NumberHelper::isOdd(5));
        self::assertFalse(NumberHelper::isOdd(4));
    }

    public function testIsPrime(): void
    {
        self::assertFalse(NumberHelper::isPrime(0));
        self::assertFalse(NumberHelper::isPrime(1));
        self::assertTrue(NumberHelper::isPrime(2));
        self::assertTrue(NumberHelper::isPrime(3));
        self::assertFalse(NumberHelper::isPrime(4));
        self::assertTrue(NumberHelper::isPrime(7));
        self::assertTrue(NumberHelper::isPrime(97));
        self::assertFalse(NumberHelper::isPrime(100));
    }

    public function testGcdLcm(): void
    {
        self::assertSame(6, NumberHelper::gcd(12, 18));
        self::assertSame(36, NumberHelper::lcm(12, 18));
        self::assertSame(0, NumberHelper::lcm(0, 5));
    }

    public function testRandomFloat(): void
    {
        $value = NumberHelper::randomFloat(0.0, 1.0);
        self::assertGreaterThanOrEqual(0.0, $value);
        self::assertLessThanOrEqual(1.0, $value);
    }

    public function testRandomInt(): void
    {
        $value = NumberHelper::randomInt(1, 10);
        self::assertGreaterThanOrEqual(1, $value);
        self::assertLessThanOrEqual(10, $value);
    }
}
