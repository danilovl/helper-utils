<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Object;

use Danilovl\HelperUtils\Helper\Object\ObjectHelper;
use PHPUnit\Framework\TestCase;

class ObjectTestUser
{
    public string $name = '';

    public int $age = 0;

    /** @phpstan-ignore property.onlyWritten */
    private string $secret = 'hidden';
}

class ObjectTestProfile
{
    public string $bio = '';

    public ?ObjectTestUser $owner = null;
}

final class ObjectHelperTest extends TestCase
{
    public function testToArray(): void
    {
        $user = new ObjectTestUser;
        $user->name = 'Alice';
        $user->age = 30;

        $array = ObjectHelper::toArray($user);
        self::assertSame('Alice', $array['name']);
        self::assertSame(30, $array['age']);
        self::assertSame('hidden', $array['secret']);
    }

    public function testToArrayDeep(): void
    {
        $profile = new ObjectTestProfile;
        $profile->bio = 'Hello';
        $profile->owner = new ObjectTestUser;
        $profile->owner->name = 'Alice';

        $array = ObjectHelper::toArray($profile, true);
        self::assertSame('Hello', $array['bio']);
        self::assertIsArray($array['owner']);
        self::assertSame('Alice', $array['owner']['name']);
    }

    public function testHydrateClass(): void
    {
        $user = ObjectHelper::hydrate(ObjectTestUser::class, ['name' => 'Bob', 'age' => 25]);
        self::assertInstanceOf(ObjectTestUser::class, $user);
        self::assertSame('Bob', $user->name);
        self::assertSame(25, $user->age);
    }

    public function testHydrateInstance(): void
    {
        $user = new ObjectTestUser;
        $user->name = 'old';
        ObjectHelper::hydrate($user, ['name' => 'new']);
        self::assertSame('new', $user->name);
    }

    public function testHydrateIgnoresUnknownKeys(): void
    {
        $user = ObjectHelper::hydrate(ObjectTestUser::class, ['name' => 'Bob', 'unknown' => 'x']);
        self::assertSame('Bob', $user->name);
    }

    public function testEqualsTrue(): void
    {
        $a = new ObjectTestUser;
        $a->name = 'Alice';
        $b = new ObjectTestUser;
        $b->name = 'Alice';
        self::assertTrue(ObjectHelper::equals($a, $b));
    }

    public function testEqualsFalseDifferentValues(): void
    {
        $a = new ObjectTestUser;
        $a->name = 'Alice';
        $b = new ObjectTestUser;
        $b->name = 'Bob';
        self::assertFalse(ObjectHelper::equals($a, $b));
    }

    public function testEqualsFalseDifferentClasses(): void
    {
        self::assertFalse(ObjectHelper::equals(new ObjectTestUser, new ObjectTestProfile));
    }

    public function testDeepEquals(): void
    {
        $a = new ObjectTestProfile;
        $a->bio = 'hi';
        $a->owner = new ObjectTestUser;
        $a->owner->name = 'Alice';

        $b = new ObjectTestProfile;
        $b->bio = 'hi';
        $b->owner = new ObjectTestUser;
        $b->owner->name = 'Alice';

        self::assertTrue(ObjectHelper::deepEquals($a, $b));
    }

    public function testDeepEqualsFalse(): void
    {
        $a = new ObjectTestProfile;
        $a->owner = new ObjectTestUser;
        $a->owner->name = 'Alice';
        $b = new ObjectTestProfile;
        $b->owner = new ObjectTestUser;
        $b->owner->name = 'Bob';

        self::assertFalse(ObjectHelper::deepEquals($a, $b));
    }

    public function testPublicProperties(): void
    {
        $user = new ObjectTestUser;
        $user->name = 'Alice';
        $props = ObjectHelper::publicProperties($user);
        self::assertArrayHasKey('name', $props);
        self::assertArrayHasKey('age', $props);
        self::assertArrayNotHasKey('secret', $props);
    }
}
