<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Array;

use Danilovl\HelperUtils\Helper\Array\ArrayMapHelper;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ArrayMapHelperTest extends TestCase
{
    public function testExtractFieldFromArrays(): void
    {
        $items = [['id' => 1, 'name' => 'A'], ['id' => 2, 'name' => 'B']];
        self::assertSame([1, 2], ArrayMapHelper::extractField($items, 'id'));
    }

    public function testExtractFieldFromPublicProperty(): void
    {
        $a = new stdClass;
        $a->id = 10;
        $b = new stdClass;
        $b->id = 20;
        self::assertSame([10, 20], ArrayMapHelper::extractField([$a, $b], 'id'));
    }

    public function testExtractFieldFromGetter(): void
    {
        $items = [
            new class() {
                public function getId(): int { return 5; }
            },
            new class() {
                public function getId(): int { return 6; }
            },
        ];
        self::assertSame([5, 6], ArrayMapHelper::extractField($items, 'id'));
    }

    public function testGroupBy(): void
    {
        $items = [
            ['type' => 'a', 'name' => 'one'],
            ['type' => 'b', 'name' => 'two'],
            ['type' => 'a', 'name' => 'three'],
        ];
        $result = ArrayMapHelper::groupBy($items, 'type');
        self::assertCount(2, $result);
        self::assertCount(2, $result['a']);
        self::assertCount(1, $result['b']);
    }

    public function testGroupByCallable(): void
    {
        $items = [1, 2, 3, 4, 5];
        $result = ArrayMapHelper::groupBy($items, static function (mixed $n): string {
            /** @var int $n */
            return $n % 2 === 0 ? 'even' : 'odd';
        });
        self::assertSame([2, 4], $result['even']);
        self::assertSame([1, 3, 5], $result['odd']);
    }

    public function testIndexBy(): void
    {
        $items = [['id' => 'a', 'v' => 1], ['id' => 'b', 'v' => 2]];
        /** @var array<string, array{id: string, v: int}> $result */
        $result = ArrayMapHelper::indexBy($items, 'id');
        self::assertSame(['a', 'b'], array_keys($result));
        self::assertSame(2, $result['b']['v']);
    }

    public function testSumBy(): void
    {
        $items = [['v' => 10], ['v' => 20], ['v' => 30]];
        self::assertSame(60, ArrayMapHelper::sumBy($items, 'v'));
    }

    public function testSumByFloat(): void
    {
        $items = [['v' => 1.5], ['v' => 2.5]];
        self::assertSame(4.0, ArrayMapHelper::sumBy($items, 'v'));
    }

    public function testAvgBy(): void
    {
        self::assertSame(20.0, ArrayMapHelper::avgBy([['v' => 10], ['v' => 20], ['v' => 30]], 'v'));
        self::assertSame(0.0, ArrayMapHelper::avgBy([], 'v'));
    }

    public function testMaxByMinBy(): void
    {
        $items = [['v' => 5], ['v' => 100], ['v' => 1]];
        /** @var array{v: int} $max */
        $max = ArrayMapHelper::maxBy($items, 'v');
        /** @var array{v: int} $min */
        $min = ArrayMapHelper::minBy($items, 'v');

        self::assertSame(100, $max['v']);
        self::assertSame(1, $min['v']);
    }

    public function testCountBy(): void
    {
        $items = [['t' => 'a'], ['t' => 'b'], ['t' => 'a'], ['t' => 'a']];
        self::assertSame(['a' => 3, 'b' => 1], ArrayMapHelper::countBy($items, 't'));
    }
}
