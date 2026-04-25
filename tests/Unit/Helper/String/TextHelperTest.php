<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\String;

use Danilovl\HelperUtils\Helper\String\TextHelper;
use PHPUnit\Framework\TestCase;

final class TextHelperTest extends TestCase
{
    public function testGenerateExcerptShort(): void
    {
        self::assertSame('short text', TextHelper::generateExcerpt('short text'));
    }

    public function testGenerateExcerptCutsAtSpace(): void
    {
        $text = 'one two three four five six seven eight nine ten';
        $result = TextHelper::generateExcerpt($text, 15);
        self::assertSame('one two three…', $result);
    }

    public function testGenerateExcerptCollapsesWhitespace(): void
    {
        $text = "lots\n\n  of   whitespace\n";
        $result = TextHelper::generateExcerpt($text);
        self::assertSame('lots of whitespace', $result);
    }

    public function testHighlight(): void
    {
        self::assertSame(
            'Hello <mark>world</mark>',
            TextHelper::highlight('Hello world', 'world')
        );
    }

    public function testHighlightCustomTags(): void
    {
        self::assertSame(
            'Hello [world]',
            TextHelper::highlight('Hello world', 'world', '[', ']')
        );
    }

    public function testHighlightEmptyNeedle(): void
    {
        self::assertSame('Hello', TextHelper::highlight('Hello', ''));
    }

    public function testLineCount(): void
    {
        self::assertSame(0, TextHelper::lineCount(''));
        self::assertSame(1, TextHelper::lineCount('one line'));
        self::assertSame(3, TextHelper::lineCount("one\ntwo\nthree"));
    }

    public function testIndent(): void
    {
        $result = TextHelper::indent("a\nb", 2);
        self::assertSame("  a\n  b", $result);
    }

    public function testIndentEmptyLine(): void
    {
        $result = TextHelper::indent("a\n\nb", 2);
        self::assertSame("  a\n\n  b", $result);
    }

    public function testDedent(): void
    {
        $input = "    a\n    b\n      c";
        $expected = "a\nb\n  c";
        self::assertSame($expected, TextHelper::dedent($input));
    }

    public function testDedentNoIndent(): void
    {
        $input = "a\nb";
        self::assertSame($input, TextHelper::dedent($input));
    }
}
