<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Array;

use Danilovl\HelperUtils\Helper\Array\CollectionHelper;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class CollectionItem
{
    public function __construct(public int $id, public string $name) {}
}

final class CollectionHelperTest extends TestCase
{
    public function testSyncOneToManyAddsAndRemoves(): void
    {
        $item1 = new CollectionItem(1, 'old1');
        $item2 = new CollectionItem(2, 'old2');
        $item2Kept = new CollectionItem(2, 'kept');
        $item3New = new CollectionItem(3, 'new');

        $current = new ArrayCollection([$item1, $item2]);
        $desired = [$item2Kept, $item3New];

        $matcher = static fn (CollectionItem $a, CollectionItem $b): bool => $a->id === $b->id;

        CollectionHelper::syncOneToMany($current, $desired, $matcher);

        $ids = array_map(static fn (CollectionItem $o): int => $o->id, $current->toArray());
        sort($ids);
        self::assertSame([2, 3], $ids);
    }

    public function testDiff(): void
    {
        $a = new ArrayCollection([1, 2, 3, 4]);
        $b = new ArrayCollection([2, 4]);
        self::assertSame([1, 3], CollectionHelper::diff($a, $b));
    }

    public function testPartition(): void
    {
        $c = new ArrayCollection([1, 2, 3, 4, 5]);
        [$even, $odd] = CollectionHelper::partition($c, static fn (int $v): bool => $v % 2 === 0);
        self::assertSame([2, 4], $even);
        self::assertSame([1, 3, 5], $odd);
    }
}
