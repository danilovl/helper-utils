<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\String;

use Danilovl\HelperUtils\Helper\String\HtmlHelper;
use PHPUnit\Framework\TestCase;

final class HtmlHelperTest extends TestCase
{
    public function testStripTags(): void
    {
        self::assertSame('Hello World', HtmlHelper::stripTags('<p>Hello <b>World</b></p>'));
    }

    public function testStripTagsWithAllowed(): void
    {
        $result = HtmlHelper::stripTags('<p>Hello <b>World</b></p>', ['p']);
        self::assertSame('<p>Hello World</p>', $result);
    }

    public function testSanitize(): void
    {
        $result = HtmlHelper::sanitize('<p>Safe</p><script>alert(1)</script>');
        self::assertStringNotContainsString('<script>', $result);
        self::assertStringContainsString('Safe', $result);
    }

    public function testTruncatePreservingTags(): void
    {
        $result = HtmlHelper::truncatePreservingTags('<p>Hello <b>World</b> beautiful day</p>', 10);
        self::assertStringEndsWith('</p>', $result);
        self::assertStringContainsString('Hello', $result);
    }

    public function testTruncatePreservingTagsShort(): void
    {
        $html = '<p>short</p>';
        self::assertSame($html, HtmlHelper::truncatePreservingTags($html, 100));
    }

    public function testExtractText(): void
    {
        $html = '<p>Hello <b>World</b></p><script>alert(1)</script>';
        self::assertSame('Hello World', HtmlHelper::extractText($html));
    }

    public function testExtractTextEntities(): void
    {
        self::assertSame('Hello & World', HtmlHelper::extractText('<p>Hello &amp; World</p>'));
    }

    public function testEscape(): void
    {
        self::assertSame('&lt;script&gt;', HtmlHelper::escape('<script>'));
    }

    public function testUnescape(): void
    {
        self::assertSame('<script>', HtmlHelper::unescape('&lt;script&gt;'));
    }

    public function testAutoLink(): void
    {
        $result = HtmlHelper::autoLink('See https://example.com for more.');
        self::assertStringContainsString('<a href="https://example.com">https://example.com</a>', $result);
    }

    public function testNl2br(): void
    {
        $result = HtmlHelper::nl2br("line1\nline2");
        self::assertStringContainsString('<br', $result);
    }

    public function testHighlightKeyword(): void
    {
        $result = HtmlHelper::highlightKeyword('Hello world wonderful world', 'world');
        self::assertSame('Hello <mark>world</mark> wonderful <mark>world</mark>', $result);
    }

    public function testHighlightKeywordEmpty(): void
    {
        self::assertSame('Hello', HtmlHelper::highlightKeyword('Hello', ''));
    }

    public function testHighlightKeywordCustomTag(): void
    {
        $result = HtmlHelper::highlightKeyword('foo', 'foo', 'em');
        self::assertSame('<em>foo</em>', $result);
    }
}
