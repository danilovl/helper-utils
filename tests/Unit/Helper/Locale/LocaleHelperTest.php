<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Locale;

use Danilovl\HelperUtils\Helper\Locale\LocaleHelper;
use PHPUnit\Framework\TestCase;

final class LocaleHelperTest extends TestCase
{
    public function testParseAcceptLanguage(): void
    {
        $result = LocaleHelper::parseAcceptLanguage('en-US,en;q=0.9,cs;q=0.8');
        self::assertCount(3, $result);
        self::assertSame('en-US', $result[0]['locale']);
        self::assertSame(1.0, $result[0]['quality']);
    }

    public function testParseAcceptLanguageSorting(): void
    {
        $result = LocaleHelper::parseAcceptLanguage('de;q=0.5,en;q=0.9');
        self::assertSame('en', $result[0]['locale']);
        self::assertSame('de', $result[1]['locale']);
    }

    public function testParseAcceptLanguageEmpty(): void
    {
        self::assertSame([], LocaleHelper::parseAcceptLanguage(''));
    }

    public function testGetLanguage(): void
    {
        self::assertSame('en', LocaleHelper::getLanguage('en_US'));
        self::assertSame('en', LocaleHelper::getLanguage('en-US'));
        self::assertSame('cs', LocaleHelper::getLanguage('cs'));
    }

    public function testGetRegion(): void
    {
        self::assertSame('US', LocaleHelper::getRegion('en_US'));
        self::assertSame('US', LocaleHelper::getRegion('en-US'));
        self::assertNull(LocaleHelper::getRegion('en'));
    }

    public function testIsValid(): void
    {
        self::assertTrue(LocaleHelper::isValid('en'));
        self::assertTrue(LocaleHelper::isValid('en_US'));
        self::assertTrue(LocaleHelper::isValid('en-US'));
        self::assertFalse(LocaleHelper::isValid(''));
        self::assertFalse(LocaleHelper::isValid('not a locale!'));
    }

    public function testIsRtl(): void
    {
        self::assertTrue(LocaleHelper::isRtl('ar'));
        self::assertTrue(LocaleHelper::isRtl('he_IL'));
        self::assertTrue(LocaleHelper::isRtl('fa'));
        self::assertFalse(LocaleHelper::isRtl('en'));
        self::assertFalse(LocaleHelper::isRtl('cs'));
    }

    public function testGetDisplayName(): void
    {
        $name = LocaleHelper::getDisplayName('en_US', 'en');
        self::assertNotEmpty($name);
    }
}
