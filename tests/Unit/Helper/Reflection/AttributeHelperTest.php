<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Reflection;

use Danilovl\HelperUtils\Helper\Reflection\AttributeHelper;
use PHPUnit\Framework\TestCase;
use Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class TestAttr
{
    public function __construct(public readonly string $value = 'default') {}
}

#[TestAttr('class')]
class AttrTestClass
{
    #[TestAttr('property')]
    public string $field = '';

    #[TestAttr('method')]
    public function action(): void {}
}

class AttrTestNoAttr {}

final class AttributeHelperTest extends TestCase
{
    public function testGetClassAttribute(): void
    {
        $attr = AttributeHelper::getClassAttribute(AttrTestClass::class, TestAttr::class);
        self::assertInstanceOf(TestAttr::class, $attr);
        self::assertSame('class', $attr->value);
    }

    public function testGetClassAttributeMissing(): void
    {
        self::assertNull(AttributeHelper::getClassAttribute(AttrTestNoAttr::class, TestAttr::class));
    }

    public function testGetClassAttributes(): void
    {
        $attrs = AttributeHelper::getClassAttributes(AttrTestClass::class, TestAttr::class);
        self::assertCount(1, $attrs);
    }

    public function testGetMethodAttribute(): void
    {
        $attr = AttributeHelper::getMethodAttribute(AttrTestClass::class, 'action', TestAttr::class);
        self::assertInstanceOf(TestAttr::class, $attr);
        self::assertSame('method', $attr->value);
    }

    public function testGetMethodAttributeMissingMethod(): void
    {
        self::assertNull(AttributeHelper::getMethodAttribute(AttrTestClass::class, 'missing', TestAttr::class));
    }

    public function testGetPropertyAttribute(): void
    {
        $attr = AttributeHelper::getPropertyAttribute(AttrTestClass::class, 'field', TestAttr::class);
        self::assertInstanceOf(TestAttr::class, $attr);
        self::assertSame('property', $attr->value);
    }

    public function testHasAttribute(): void
    {
        self::assertTrue(AttributeHelper::hasAttribute(AttrTestClass::class, TestAttr::class));
        self::assertFalse(AttributeHelper::hasAttribute(AttrTestNoAttr::class, TestAttr::class));
    }

    public function testGetEntityTableNameDefault(): void
    {
        // Without ORM\Table attribute, returns lowercased short name
        self::assertSame('attrtestclass', AttributeHelper::getEntityTableName(AttrTestClass::class));
    }

    public function testGetEntityTableNameFromObject(): void
    {
        self::assertSame('attrtestnoattr', AttributeHelper::getEntityTableName(new AttrTestNoAttr));
    }
}
