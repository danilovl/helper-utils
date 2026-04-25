<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Locale;

use Danilovl\HelperUtils\Helper\Locale\CountryHelper;
use PHPUnit\Framework\TestCase;

final class CountryHelperTest extends TestCase
{
    public function testGetName(): void
    {
        $name = CountryHelper::getName('US', 'en');
        self::assertNotEmpty($name);
        self::assertNotSame('US', $name);
    }

    public function testAlpha2ToAlpha3(): void
    {
        self::assertSame('USA', CountryHelper::alpha2ToAlpha3('US'));
        self::assertSame('CZE', CountryHelper::alpha2ToAlpha3('CZ'));
        self::assertSame('USA', CountryHelper::alpha2ToAlpha3('us'));
    }

    public function testAlpha2ToAlpha3Invalid(): void
    {
        self::assertNull(CountryHelper::alpha2ToAlpha3('XX'));
    }

    public function testAlpha3ToAlpha2(): void
    {
        self::assertSame('US', CountryHelper::alpha3ToAlpha2('USA'));
        self::assertSame('CZ', CountryHelper::alpha3ToAlpha2('CZE'));
    }

    public function testAlpha3ToAlpha2Invalid(): void
    {
        self::assertNull(CountryHelper::alpha3ToAlpha2('XXX'));
    }

    public function testGetFlagEmoji(): void
    {
        self::assertSame('🇨🇿', CountryHelper::getFlagEmoji('CZ'));
        self::assertSame('🇺🇸', CountryHelper::getFlagEmoji('US'));
    }

    public function testGetFlagEmojiInvalid(): void
    {
        self::assertSame('', CountryHelper::getFlagEmoji('X1'));
    }

    public function testGetCallingCode(): void
    {
        self::assertSame('+420', CountryHelper::getCallingCode('CZ'));
        self::assertSame('+1', CountryHelper::getCallingCode('US'));
        self::assertNull(CountryHelper::getCallingCode('XX'));
    }

    public function testIsValid(): void
    {
        self::assertTrue(CountryHelper::isValid('US'));
        self::assertTrue(CountryHelper::isValid('CZ'));
        self::assertFalse(CountryHelper::isValid('XX'));
    }

    public function testGetAllAlpha2(): void
    {
        $codes = CountryHelper::getAllAlpha2();
        self::assertNotEmpty($codes);
        self::assertContains('US', $codes);
        self::assertContains('CZ', $codes);
    }
}
