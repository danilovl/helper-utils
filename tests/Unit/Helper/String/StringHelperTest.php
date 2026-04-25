<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\String;

use Danilovl\HelperUtils\Helper\String\StringHelper;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

final class StringHelperTest extends TestCase
{
    public function testTruncate(): void
    {
        self::assertSame('Hello', StringHelper::truncate('Hello', 10));
        self::assertSame('Hell…', StringHelper::truncate('Hello World', 5));
    }

    public function testTruncateUnicode(): void
    {
        self::assertSame('Прив…', StringHelper::truncate('Привет мир', 5));
    }

    public function testTruncateCustomSuffix(): void
    {
        self::assertSame('Hello...', StringHelper::truncate('Hello World!', 8, '...'));
    }

    public function testTruncateWords(): void
    {
        self::assertSame('one two…', StringHelper::truncateWords('one two three four', 2));
        self::assertSame('one two', StringHelper::truncateWords('one two', 2));
    }

    public function testStartsEndsWith(): void
    {
        self::assertTrue(StringHelper::startsWith('Hello World', 'Hello'));
        self::assertFalse(StringHelper::startsWith('Hello World', 'World'));
        self::assertTrue(StringHelper::endsWith('Hello World', 'World'));
        self::assertFalse(StringHelper::endsWith('Hello World', 'Hello'));
    }

    public function testContains(): void
    {
        self::assertTrue(StringHelper::contains('Hello World', 'lo W'));
        self::assertFalse(StringHelper::contains('Hello World', 'foo'));
        self::assertFalse(StringHelper::contains('Hello World', 'hello'));
        self::assertTrue(StringHelper::contains('Hello World', 'hello', true));
    }

    public function testContainsAny(): void
    {
        self::assertTrue(StringHelper::containsAny('Hello World', ['foo', 'World']));
        self::assertFalse(StringHelper::containsAny('Hello World', ['foo', 'bar']));
    }

    public function testCamelToSnake(): void
    {
        self::assertSame('hello_world', StringHelper::camelToSnake('helloWorld'));
        self::assertSame('hello_world', StringHelper::camelToSnake('HelloWorld'));
        self::assertSame('html_parser', StringHelper::camelToSnake('HTMLParser'));
    }

    public function testSnakeToCamel(): void
    {
        self::assertSame('helloWorld', StringHelper::snakeToCamel('hello_world'));
        self::assertSame('HelloWorld', StringHelper::snakeToCamel('hello_world', true));
    }

    public function testKebabToCamel(): void
    {
        self::assertSame('helloWorld', StringHelper::kebabToCamel('hello-world'));
    }

    public function testPascalCase(): void
    {
        self::assertSame('HelloWorld', StringHelper::pascalCase('hello-world'));
        self::assertSame('HelloWorld', StringHelper::pascalCase('hello_world'));
    }

    public function testSlugify(): void
    {
        self::assertSame('hello-world', StringHelper::slugify('Hello World'));
        self::assertSame('privet-mir', StringHelper::slugify('Привет мир'));
        self::assertSame('hello_world', StringHelper::slugify('Hello World', '_'));
    }

    public function testRemoveAccents(): void
    {
        self::assertSame('cafe', mb_strtolower(StringHelper::removeAccents('café')));
        self::assertSame('naive', mb_strtolower(StringHelper::removeAccents('naïve')));
    }

    public function testRandomLength(): void
    {
        self::assertSame(16, mb_strlen(StringHelper::random(16)));
        self::assertSame(8, mb_strlen(StringHelper::random(8, 'numeric')));
        self::assertMatchesRegularExpression('/^[0-9]+$/', StringHelper::random(10, 'numeric'));
        self::assertMatchesRegularExpression('/^[0-9a-f]+$/', StringHelper::random(10, 'hex'));
        self::assertMatchesRegularExpression('/^[a-zA-Z]+$/', StringHelper::random(10, 'alpha'));
    }

    public function testUuid(): void
    {
        $uuid = StringHelper::uuid();
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $uuid
        );
    }

    public function testMask(): void
    {
        self::assertSame('1234******7890', StringHelper::mask('12345678907890', 4, 4));
        self::assertSame('****', StringHelper::mask('abcd', 4, 4));
    }

    public function testMaskEmail(): void
    {
        self::assertSame('j*********n@example.com', StringHelper::maskEmail('john.doe.an@example.com'));
        self::assertSame('**@x.com', StringHelper::maskEmail('ab@x.com'));
    }

    public function testMaskPhone(): void
    {
        self::assertSame('***-***-1234', StringHelper::maskPhone('555-555-1234'));
    }

    public function testReverse(): void
    {
        self::assertSame('olleH', StringHelper::reverse('Hello'));
        self::assertSame('тевирП', StringHelper::reverse('Привет'));
    }

    public function testLength(): void
    {
        self::assertSame(5, StringHelper::length('Hello'));
        self::assertSame(6, StringHelper::length('Привет'));
    }

    public function testWordCount(): void
    {
        self::assertSame(3, StringHelper::wordCount('hello world foo'));
        self::assertSame(0, StringHelper::wordCount(''));
        self::assertSame(0, StringHelper::wordCount('   '));
    }

    public function testReadingTime(): void
    {
        $text = str_repeat('word ', 400); // 400 words
        self::assertSame(2, StringHelper::readingTime($text));
        self::assertSame(1, StringHelper::readingTime('only a few words'));
    }

    public function testReadingTimeInvalidWpm(): void
    {
        $this->expectException(InvalidArgumentException::class);
        StringHelper::readingTime('text', 0);
    }

    public function testLevenshtein(): void
    {
        self::assertSame(0, StringHelper::levenshtein('hello', 'hello'));
        self::assertSame(1, StringHelper::levenshtein('hello', 'helli'));
    }

    public function testSimilarity(): void
    {
        self::assertSame(1.0, StringHelper::similarity('hello', 'hello'));
        self::assertSame(1.0, StringHelper::similarity('', ''));
        self::assertGreaterThan(0.0, StringHelper::similarity('hello', 'world'));
    }

    public function testPluralize(): void
    {
        self::assertSame('cats', StringHelper::pluralize('cat'));
        self::assertSame('boxes', StringHelper::pluralize('box'));
        self::assertSame('cities', StringHelper::pluralize('city'));
        self::assertSame('mice', StringHelper::pluralize('mouse'));
    }

    public function testSingularize(): void
    {
        self::assertSame('cat', StringHelper::singularize('cats'));
        self::assertSame('box', StringHelper::singularize('boxes'));
        self::assertSame('city', StringHelper::singularize('cities'));
    }
}
