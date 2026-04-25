<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Object;

use Danilovl\HelperUtils\Helper\Object\CloneHelper;
use PHPUnit\Framework\TestCase;

class CloneTestNested
{
    public int $value = 42;
}

class CloneTestParent
{
    public string $name = 'original';

    public ?CloneTestNested $nested = null;
}

final class CloneHelperTest extends TestCase
{
    public function testShallowCloneIsClone(): void
    {
        $original = new CloneTestParent;
        $clone = CloneHelper::shallowClone($original);
        self::assertNotSame($original, $clone);
        self::assertSame($original->name, $clone->name);
    }

    public function testShallowCloneSharesNested(): void
    {
        $original = new CloneTestParent;
        $original->nested = new CloneTestNested;
        $clone = CloneHelper::shallowClone($original);
        self::assertSame($original->nested, $clone->nested);
    }

    public function testDeepCloneCopiesNested(): void
    {
        $original = new CloneTestParent;
        $original->nested = new CloneTestNested;
        $clone = CloneHelper::deepClone($original);
        self::assertNotNull($original->nested);
        self::assertNotNull($clone->nested);
        self::assertNotSame($original->nested, $clone->nested);
        self::assertSame($original->nested->value, $clone->nested->value);
    }

    public function testDeepCloneIndependent(): void
    {
        $original = new CloneTestParent;
        $original->nested = new CloneTestNested;
        $clone = CloneHelper::deepClone($original);
        self::assertNotNull($clone->nested);
        self::assertNotNull($original->nested);
        $clone->nested->value = 99;
        self::assertSame(42, $original->nested->value);
    }

    public function testShallowCloneAll(): void
    {
        $a = new CloneTestParent;
        $a->name = 'a';
        $b = new CloneTestParent;
        $b->name = 'b';
        $clones = CloneHelper::shallowCloneAll([$a, $b]);
        self::assertCount(2, $clones);
        self::assertNotSame($a, $clones[0]);
        self::assertSame('a', $clones[0]->name);
    }

    public function testDeepCloneAll(): void
    {
        $a = new CloneTestParent;
        $a->nested = new CloneTestNested;
        $clones = CloneHelper::deepCloneAll([$a]);
        self::assertNotSame($a->nested, $clones[0]->nested);
    }

    public function testCloneWith(): void
    {
        $original = new CloneTestParent;
        $clone = CloneHelper::cloneWith($original, static function (CloneTestParent $obj): void {
            $obj->name = 'modified';
        });
        self::assertNotSame($original, $clone);
        self::assertSame('original', $original->name);
        self::assertSame('modified', $clone->name);
    }
}
