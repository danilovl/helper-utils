<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Reflection;

use Danilovl\HelperUtils\Exception\HelperException;
use Danilovl\HelperUtils\Helper\Reflection\ReflectionHelper;
use PHPUnit\Framework\TestCase;

class ReflectionTestParent
{
    protected string $parentProp = 'parent_value';
}

class ReflectionTestChild extends ReflectionTestParent
{
    /** @phpstan-ignore property.onlyWritten */
    private string $childProp = 'child_value';

    public function publicMethod(): string
    {
        return 'public';
    }

    /** @phpstan-ignore method.unused */
    private function privateMethod(int $x, int $y): int
    {
        return $x + $y;
    }
}

interface ReflectionTestInterface {}
class ReflectionTestImpl implements ReflectionTestInterface {}

final class ReflectionHelperTest extends TestCase
{
    public function testGetShortName(): void
    {
        self::assertSame('ReflectionTestChild', ReflectionHelper::getShortName(ReflectionTestChild::class));
        self::assertSame('ReflectionTestChild', ReflectionHelper::getShortName(new ReflectionTestChild));
    }

    public function testGetNamespace(): void
    {
        self::assertSame(__NAMESPACE__, ReflectionHelper::getNamespace(ReflectionTestChild::class));
    }

    public function testGetPropertyValue(): void
    {
        $obj = new ReflectionTestChild;
        self::assertSame('child_value', ReflectionHelper::getPropertyValue($obj, 'childProp'));
        self::assertSame('parent_value', ReflectionHelper::getPropertyValue($obj, 'parentProp'));
    }

    public function testSetPropertyValue(): void
    {
        $obj = new ReflectionTestChild;
        ReflectionHelper::setPropertyValue($obj, 'childProp', 'new_value');
        self::assertSame('new_value', ReflectionHelper::getPropertyValue($obj, 'childProp'));
    }

    public function testHasProperty(): void
    {
        $obj = new ReflectionTestChild;
        self::assertTrue(ReflectionHelper::hasProperty($obj, 'childProp'));
        self::assertTrue(ReflectionHelper::hasProperty($obj, 'parentProp'));
        self::assertFalse(ReflectionHelper::hasProperty($obj, 'missing'));
    }

    public function testGetPropertyValueMissing(): void
    {
        $this->expectException(HelperException::class);
        ReflectionHelper::getPropertyValue(new ReflectionTestChild, 'missing');
    }

    public function testGetAllProperties(): void
    {
        $obj = new ReflectionTestChild;
        $props = ReflectionHelper::getAllProperties($obj);
        self::assertArrayHasKey('childProp', $props);
        self::assertSame('child_value', $props['childProp']);
    }

    public function testCallPrivateMethod(): void
    {
        $obj = new ReflectionTestChild;
        self::assertSame(7, ReflectionHelper::callPrivateMethod($obj, 'privateMethod', [3, 4]));
    }

    public function testCallPrivateMethodMissing(): void
    {
        $this->expectException(HelperException::class);
        ReflectionHelper::callPrivateMethod(new ReflectionTestChild, 'missing');
    }

    public function testGetParentClasses(): void
    {
        $parents = ReflectionHelper::getParentClasses(ReflectionTestChild::class);
        self::assertSame([ReflectionTestParent::class], $parents);
    }

    public function testGetInterfaces(): void
    {
        $interfaces = ReflectionHelper::getInterfaces(ReflectionTestImpl::class);
        self::assertContains(ReflectionTestInterface::class, $interfaces);
    }

    public function testGetTraits(): void
    {
        $traits = ReflectionHelper::getTraits(ReflectionTestChild::class);
        self::assertSame([], $traits);
    }
}
