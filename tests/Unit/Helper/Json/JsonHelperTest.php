<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Json;

use Danilovl\HelperUtils\Helper\Json\JsonHelper;
use JsonException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class JsonHelperTest extends TestCase
{
    public function testDecodeAssoc(): void
    {
        $result = JsonHelper::decode('{"a":1,"b":2}');
        self::assertSame(['a' => 1, 'b' => 2], $result);
    }

    public function testDecodeObject(): void
    {
        /** @var stdClass $result */
        $result = JsonHelper::decode('{"a":1}', false);
        self::assertSame(1, $result->a);
    }

    public function testDecodeInvalidThrows(): void
    {
        $this->expectException(JsonException::class);
        JsonHelper::decode('{invalid}');
    }

    public function testTryDecodeReturnsNull(): void
    {
        self::assertNull(JsonHelper::tryDecode('{invalid}'));
        self::assertSame(['a' => 1], JsonHelper::tryDecode('{"a":1}'));
    }

    public function testEncode(): void
    {
        self::assertSame('{"a":1}', JsonHelper::encode(['a' => 1]));
    }

    public function testEncodeInvalidThrows(): void
    {
        $this->expectException(JsonException::class);
        $resource = fopen('php://memory', 'r');

        try {
            JsonHelper::encode($resource);
        } finally {
            if (is_resource($resource)) {
                fclose($resource);
            }
        }
    }

    public function testTryEncodeReturnsNull(): void
    {
        $resource = fopen('php://memory', 'r');

        try {
            self::assertNull(JsonHelper::tryEncode($resource));
        } finally {
            if (is_resource($resource)) {
                fclose($resource);
            }
        }
        self::assertSame('{"a":1}', JsonHelper::tryEncode(['a' => 1]));
    }

    public function testPretty(): void
    {
        $result = JsonHelper::pretty(['a' => 'тест']);
        self::assertStringContainsString("\n", $result);
        self::assertStringContainsString('тест', $result);
    }

    public function testIsValid(): void
    {
        self::assertTrue(JsonHelper::isValid('{"a":1}'));
        self::assertTrue(JsonHelper::isValid('[1,2,3]'));
        self::assertTrue(JsonHelper::isValid('null'));
        self::assertFalse(JsonHelper::isValid('{invalid}'));
    }

    public function testDeepMerge(): void
    {
        $a = '{"x":1,"nested":{"a":1,"b":2}}';
        $b = '{"y":2,"nested":{"b":99,"c":3}}';
        $result = JsonHelper::deepMerge($a, $b);
        $decoded = JsonHelper::decode($result);
        self::assertSame(['x' => 1, 'nested' => ['a' => 1, 'b' => 99, 'c' => 3], 'y' => 2], $decoded);
    }
}
