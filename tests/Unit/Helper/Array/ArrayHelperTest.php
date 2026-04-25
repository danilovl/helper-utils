<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Array;

use Danilovl\HelperUtils\Helper\Array\ArrayHelper;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use stdClass;

final class ArrayHelperTest extends TestCase
{
    public function testOnly(): void
    {
        $result = ArrayHelper::only(['a' => 1, 'b' => 2, 'c' => 3], ['a', 'c']);
        self::assertSame(['a' => 1, 'c' => 3], $result);
    }

    public function testExcept(): void
    {
        $result = ArrayHelper::except(['a' => 1, 'b' => 2, 'c' => 3], ['b']);
        self::assertSame(['a' => 1, 'c' => 3], $result);
    }

    public function testIsAssociative(): void
    {
        self::assertTrue(ArrayHelper::isAssociative(['a' => 1, 'b' => 2]));
        self::assertFalse(ArrayHelper::isAssociative([1, 2, 3]));
        self::assertFalse(ArrayHelper::isAssociative([]));
    }

    public function testIsList(): void
    {
        self::assertTrue(ArrayHelper::isList([1, 2, 3]));
        self::assertTrue(ArrayHelper::isList([]));
        self::assertFalse(ArrayHelper::isList(['a' => 1]));
    }

    public function testFlatten(): void
    {
        $result = ArrayHelper::flatten([1, [2, [3, [4]]]]);
        self::assertSame([1, 2, 3, 4], $result);
    }

    public function testFlattenWithDepth(): void
    {
        $result = ArrayHelper::flatten([1, [2, [3, [4]]]], 1);
        self::assertSame([1, 2, [3, [4]]], $result);
    }

    public function testFlattenWithDots(): void
    {
        $result = ArrayHelper::flattenWithDots(['a' => ['b' => ['c' => 1]], 'd' => 2]);
        self::assertSame(['a.b.c' => 1, 'd' => 2], $result);
    }

    public function testGet(): void
    {
        $array = ['a' => ['b' => ['c' => 42]]];
        self::assertSame(42, ArrayHelper::get($array, 'a.b.c'));
        self::assertNull(ArrayHelper::get($array, 'a.b.x'));
        self::assertSame('default', ArrayHelper::get($array, 'a.b.x', 'default'));
    }

    public function testGetTopLevelKey(): void
    {
        self::assertSame(1, ArrayHelper::get(['a.b' => 1], 'a.b'));
    }

    public function testSet(): void
    {
        $array = [];
        ArrayHelper::set($array, 'a.b.c', 42);
        self::assertSame(['a' => ['b' => ['c' => 42]]], $array);
    }

    public function testHas(): void
    {
        $array = ['a' => ['b' => ['c' => 42]]];
        self::assertTrue(ArrayHelper::has($array, 'a.b.c'));
        self::assertFalse(ArrayHelper::has($array, 'a.b.x'));
    }

    public function testForget(): void
    {
        $array = ['a' => ['b' => 1, 'c' => 2]];
        ArrayHelper::forget($array, 'a.b');
        self::assertSame(['a' => ['c' => 2]], $array);
    }

    public function testRecursiveMerge(): void
    {
        $a = ['x' => 1, 'nested' => ['a' => 1, 'b' => 2]];
        $b = ['y' => 2, 'nested' => ['b' => 99, 'c' => 3]];
        $result = ArrayHelper::recursiveMerge($a, $b);
        self::assertSame(['x' => 1, 'nested' => ['a' => 1, 'b' => 99, 'c' => 3], 'y' => 2], $result);
    }

    public function testRecursiveDiff(): void
    {
        $a = ['x' => 1, 'y' => 2, 'nested' => ['a' => 1, 'b' => 2]];
        $b = ['x' => 1, 'nested' => ['a' => 1]];
        $result = ArrayHelper::recursiveDiff($a, $b);
        self::assertSame(['y' => 2, 'nested' => ['b' => 2]], $result);
    }

    public function testPartition(): void
    {
        [$even, $odd] = ArrayHelper::partition([1, 2, 3, 4, 5], static fn (int $v): bool => $v % 2 === 0);
        self::assertSame([1 => 2, 3 => 4], $even);
        self::assertSame([0 => 1, 2 => 3, 4 => 5], $odd);
    }

    public function testChunk(): void
    {
        $result = ArrayHelper::chunk([1, 2, 3, 4, 5], 2);
        self::assertSame([[1, 2], [3, 4], [5]], $result);
    }

    public function testChunkInvalidSize(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ArrayHelper::chunk([1, 2, 3], 0);
    }

    public function testShuffle(): void
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3];
        $result = ArrayHelper::shuffle($array);
        self::assertSame([1, 2, 3], array_values(array_intersect([1, 2, 3], $result)));
        self::assertCount(3, $result);
    }

    public function testPluck(): void
    {
        $array = [['name' => 'Alice'], ['name' => 'Bob']];
        self::assertSame(['Alice', 'Bob'], ArrayHelper::pluck($array, 'name'));
    }

    public function testPluckObject(): void
    {
        $a = new stdClass;
        $a->id = 1;
        $b = new stdClass;
        $b->id = 2;
        self::assertSame([1, 2], ArrayHelper::pluck([$a, $b], 'id'));
    }

    public function testUnique(): void
    {
        self::assertSame([1, 2, 3], ArrayHelper::unique([1, 2, 2, 3, 1]));
    }

    public function testFindFirst(): void
    {
        $result = ArrayHelper::findFirst([1, 2, 3, 4], static fn (int $v): bool => $v > 2);
        self::assertSame(3, $result);
    }

    public function testFindFirstReturnsNull(): void
    {
        /** @phpstan-ignore greater.alwaysFalse */
        self::assertNull(ArrayHelper::findFirst([1, 2], static fn (mixed $v): bool => $v > 99));
    }

    public function testFindLast(): void
    {
        $result = ArrayHelper::findLast([1, 2, 3, 4], static fn (int $v): bool => $v < 4);
        self::assertSame(3, $result);
    }
}
