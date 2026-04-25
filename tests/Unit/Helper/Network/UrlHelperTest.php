<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Network;

use Danilovl\HelperUtils\Helper\Network\UrlHelper;
use PHPUnit\Framework\TestCase;

final class UrlHelperTest extends TestCase
{
    public function testIsAbsolute(): void
    {
        self::assertTrue(UrlHelper::isAbsolute('https://example.com'));
        self::assertTrue(UrlHelper::isAbsolute('//example.com'));
        self::assertFalse(UrlHelper::isAbsolute('/path'));
        self::assertFalse(UrlHelper::isAbsolute('path'));
    }

    public function testIsValid(): void
    {
        self::assertTrue(UrlHelper::isValid('https://example.com/path'));
        self::assertFalse(UrlHelper::isValid('not a url'));
    }

    public function testGetDomain(): void
    {
        self::assertSame('example.com', UrlHelper::getDomain('https://example.com/path'));
        self::assertNull(UrlHelper::getDomain('not a url'));
    }

    public function testGetRootDomain(): void
    {
        self::assertSame('example.com', UrlHelper::getRootDomain('https://foo.bar.example.com'));
        self::assertSame('example.com', UrlHelper::getRootDomain('https://example.com'));
        self::assertSame('example.co.uk', UrlHelper::getRootDomain('https://www.example.co.uk'));
    }

    public function testGetScheme(): void
    {
        self::assertSame('https', UrlHelper::getScheme('https://example.com'));
        self::assertSame('http', UrlHelper::getScheme('http://example.com'));
    }

    public function testIsExternal(): void
    {
        self::assertTrue(UrlHelper::isExternal('https://other.com/path', 'example.com'));
        self::assertFalse(UrlHelper::isExternal('https://example.com/path', 'example.com'));
        self::assertFalse(UrlHelper::isExternal('https://EXAMPLE.com/path', 'example.com'));
    }

    public function testAddQueryParams(): void
    {
        $result = UrlHelper::addQueryParams('https://example.com/path', ['foo' => 'bar']);
        self::assertStringContainsString('foo=bar', $result);
    }

    public function testAddQueryParamsMergesExisting(): void
    {
        $result = UrlHelper::addQueryParams('https://example.com?a=1', ['b' => '2']);
        self::assertStringContainsString('a=1', $result);
        self::assertStringContainsString('b=2', $result);
    }

    public function testRemoveQueryParam(): void
    {
        $result = UrlHelper::removeQueryParam('https://example.com?a=1&b=2', 'a');
        self::assertStringNotContainsString('a=1', $result);
        self::assertStringContainsString('b=2', $result);
    }

    public function testGetQueryParam(): void
    {
        self::assertSame('1', UrlHelper::getQueryParam('https://example.com?a=1&b=2', 'a'));
        self::assertNull(UrlHelper::getQueryParam('https://example.com', 'missing'));
    }

    public function testMakeAbsolute(): void
    {
        self::assertSame(
            'https://example.com/path',
            UrlHelper::makeAbsolute('/path', 'https://example.com')
        );
        self::assertSame(
            'https://example.com/already',
            UrlHelper::makeAbsolute('https://example.com/already', 'https://other.com')
        );
    }

    public function testEnsureTrailingSlash(): void
    {
        self::assertSame('https://example.com/', UrlHelper::ensureTrailingSlash('https://example.com'));
        self::assertSame('https://example.com/', UrlHelper::ensureTrailingSlash('https://example.com/'));
    }

    public function testRemoveTrailingSlash(): void
    {
        self::assertSame('https://example.com', UrlHelper::removeTrailingSlash('https://example.com/'));
    }

    public function testBuildQueryString(): void
    {
        self::assertSame('a=1&b=2', UrlHelper::buildQueryString(['a' => 1, 'b' => 2]));
    }
}
