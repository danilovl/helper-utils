<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Compare;

use Danilovl\HelperUtils\Helper\Compare\VersionHelper;
use PHPUnit\Framework\TestCase;

final class VersionHelperTest extends TestCase
{
    public function testCompare(): void
    {
        self::assertSame(0, VersionHelper::compare('1.0.0', '1.0.0'));
        self::assertSame(-1, VersionHelper::compare('1.0.0', '1.0.1'));
        self::assertSame(1, VersionHelper::compare('2.0.0', '1.0.0'));
    }

    public function testIsGreater(): void
    {
        self::assertTrue(VersionHelper::isGreater('2.0.0', '1.0.0'));
        self::assertFalse(VersionHelper::isGreater('1.0.0', '1.0.0'));
        self::assertFalse(VersionHelper::isGreater('1.0.0', '2.0.0'));
    }

    public function testSatisfiesCaret(): void
    {
        self::assertTrue(VersionHelper::satisfies('1.5.0', '^1.0'));
        self::assertTrue(VersionHelper::satisfies('1.5.0', '^1.5'));
        self::assertFalse(VersionHelper::satisfies('2.0.0', '^1.0'));
        self::assertFalse(VersionHelper::satisfies('0.9.0', '^1.0'));
    }

    public function testSatisfiesTilde(): void
    {
        self::assertTrue(VersionHelper::satisfies('1.2.5', '~1.2'));
        self::assertFalse(VersionHelper::satisfies('1.3.0', '~1.2'));
        self::assertFalse(VersionHelper::satisfies('2.0.0', '~1.2'));
    }

    public function testSatisfiesComparisonOperators(): void
    {
        self::assertTrue(VersionHelper::satisfies('2.0.0', '>=1.0'));
        self::assertTrue(VersionHelper::satisfies('1.0.0', '>=1.0'));
        self::assertFalse(VersionHelper::satisfies('0.9', '>=1.0'));
        self::assertTrue(VersionHelper::satisfies('1.0.0', '<2.0'));
    }

    public function testSatisfiesExact(): void
    {
        self::assertTrue(VersionHelper::satisfies('1.0.0', '1.0.0'));
        self::assertFalse(VersionHelper::satisfies('1.0.1', '1.0.0'));
    }

    public function testParts(): void
    {
        self::assertSame(1, VersionHelper::major('1.2.3'));
        self::assertSame(2, VersionHelper::minor('1.2.3'));
        self::assertSame(3, VersionHelper::patch('1.2.3'));
    }

    public function testPartsWithVPrefix(): void
    {
        self::assertSame(1, VersionHelper::major('v1.2.3'));
    }

    public function testPartsWithSuffix(): void
    {
        self::assertSame(1, VersionHelper::major('1.2.3-beta'));
        self::assertSame(2, VersionHelper::minor('1.2.3+build'));
    }
}
