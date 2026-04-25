<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Compare;

use Danilovl\HelperUtils\Enum\ComparisonOperator;
use Danilovl\HelperUtils\Helper\Compare\CompareHelper;
use PHPUnit\Framework\TestCase;

final class CompareHelperTest extends TestCase
{
    public function testCompareEqual(): void
    {
        self::assertTrue(CompareHelper::compare(1, ComparisonOperator::EQUAL, '1'));
        self::assertTrue(CompareHelper::compare(1, ComparisonOperator::STRICT_EQUAL, 1));
        self::assertFalse(CompareHelper::compare(1, ComparisonOperator::STRICT_EQUAL, '1'));
    }

    public function testCompareNotEqual(): void
    {
        self::assertTrue(CompareHelper::compare(1, ComparisonOperator::NOT_EQUAL, 2));
        self::assertFalse(CompareHelper::compare(1, ComparisonOperator::NOT_EQUAL, '1'));
        self::assertTrue(CompareHelper::compare(1, ComparisonOperator::STRICT_NOT_EQUAL, '1'));
    }

    public function testCompareGreaterLess(): void
    {
        self::assertTrue(CompareHelper::compare(5, ComparisonOperator::GREATER_THAN, 3));
        self::assertTrue(CompareHelper::compare(5, ComparisonOperator::GREATER_OR_EQUAL, 5));
        self::assertTrue(CompareHelper::compare(3, ComparisonOperator::LESS_THAN, 5));
        self::assertTrue(CompareHelper::compare(5, ComparisonOperator::LESS_OR_EQUAL, 5));
    }

    public function testCompareBetween(): void
    {
        self::assertTrue(CompareHelper::compare(5, ComparisonOperator::BETWEEN, [1, 10]));
        self::assertFalse(CompareHelper::compare(15, ComparisonOperator::BETWEEN, [1, 10]));
    }

    public function testCompareIn(): void
    {
        self::assertTrue(CompareHelper::compare('a', ComparisonOperator::IN, ['a', 'b', 'c']));
        self::assertFalse(CompareHelper::compare('z', ComparisonOperator::IN, ['a', 'b', 'c']));
        self::assertTrue(CompareHelper::compare('z', ComparisonOperator::NOT_IN, ['a', 'b', 'c']));
    }

    public function testCompareString(): void
    {
        self::assertTrue(CompareHelper::compare('hello world', ComparisonOperator::CONTAINS, 'lo wo'));
        self::assertTrue(CompareHelper::compare('hello world', ComparisonOperator::STARTS_WITH, 'hello'));
        self::assertTrue(CompareHelper::compare('hello world', ComparisonOperator::ENDS_WITH, 'world'));
        self::assertTrue(CompareHelper::compare('hello123', ComparisonOperator::MATCHES_REGEX, '/^[a-z]+\d+$/'));
    }

    public function testBetweenInclusive(): void
    {
        self::assertTrue(CompareHelper::between(5, 1, 10));
        self::assertTrue(CompareHelper::between(1, 1, 10));
        self::assertTrue(CompareHelper::between(10, 1, 10));
        self::assertFalse(CompareHelper::between(11, 1, 10));
    }

    public function testBetweenExclusive(): void
    {
        self::assertTrue(CompareHelper::between(5, 1, 10, false));
        self::assertFalse(CompareHelper::between(1, 1, 10, false));
        self::assertFalse(CompareHelper::between(10, 1, 10, false));
    }

    public function testEqualsAny(): void
    {
        self::assertTrue(CompareHelper::equalsAny('a', ['a', 'b', 'c']));
        self::assertFalse(CompareHelper::equalsAny('z', ['a', 'b', 'c']));
        self::assertTrue(CompareHelper::equalsAny('1', [1, 2], false));
        self::assertFalse(CompareHelper::equalsAny('1', [1, 2], true));
    }

    public function testSpaceshipDeepArrays(): void
    {
        self::assertSame(0, CompareHelper::spaceshipDeep([1, 2, 3], [1, 2, 3]));
        self::assertSame(-1, CompareHelper::spaceshipDeep([1, 2], [1, 2, 3]));
        self::assertSame(1, CompareHelper::spaceshipDeep([1, 2, 3], [1, 2]));
        self::assertSame(-1, CompareHelper::spaceshipDeep([1, 2, 3], [1, 2, 4]));
    }

    public function testSpaceshipDeepScalars(): void
    {
        self::assertSame(0, CompareHelper::spaceshipDeep(5, 5));
        self::assertSame(-1, CompareHelper::spaceshipDeep(3, 5));
        self::assertSame(1, CompareHelper::spaceshipDeep(5, 3));
    }
}
