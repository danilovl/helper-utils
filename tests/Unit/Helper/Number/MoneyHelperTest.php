<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Number;

use Danilovl\HelperUtils\Helper\Number\MoneyHelper;
use PHPUnit\Framework\TestCase;

final class MoneyHelperTest extends TestCase
{
    public function testMinorToMajorUsd(): void
    {
        self::assertSame(12.34, MoneyHelper::minorToMajor(1_234, 'USD'));
        self::assertSame(0.0, MoneyHelper::minorToMajor(0));
    }

    public function testMinorToMajorJpy(): void
    {
        self::assertSame(1_234.0, MoneyHelper::minorToMajor(1_234, 'JPY'));
    }

    public function testMinorToMajorKwd(): void
    {
        self::assertSame(1.234, MoneyHelper::minorToMajor(1_234, 'KWD'));
    }

    public function testMajorToMinorUsd(): void
    {
        self::assertSame(1_234, MoneyHelper::majorToMinor(12.34, 'USD'));
    }

    public function testMajorToMinorJpy(): void
    {
        self::assertSame(1_234, MoneyHelper::majorToMinor(1_234.0, 'JPY'));
    }

    public function testMajorToMinorRoundsCorrectly(): void
    {
        self::assertSame(100, MoneyHelper::majorToMinor(0.999, 'USD'));
    }

    public function testFormat(): void
    {
        $result = MoneyHelper::format(1_234, 'USD', 'en');
        self::assertStringContainsString('12.34', $result);
    }

    public function testFormatJpy(): void
    {
        $result = MoneyHelper::format(1_234, 'JPY', 'en');
        self::assertStringContainsString('1,234', $result);
    }

    public function testGetCurrencySymbol(): void
    {
        $usd = MoneyHelper::getCurrencySymbol('USD', 'en');
        self::assertNotEmpty($usd);
    }
}
