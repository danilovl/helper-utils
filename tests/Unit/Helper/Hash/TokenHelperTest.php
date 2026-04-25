<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Hash;

use Danilovl\HelperUtils\Helper\Hash\TokenHelper;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

final class TokenHelperTest extends TestCase
{
    public function testGenerate(): void
    {
        $token = TokenHelper::generate(32);
        self::assertSame(32, mb_strlen($token));
        self::assertMatchesRegularExpression('/^[0-9a-f]+$/', $token);
    }

    public function testGenerateUnique(): void
    {
        self::assertNotSame(TokenHelper::generate(), TokenHelper::generate());
    }

    public function testGenerateInvalidLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        /** @phpstan-ignore argument.type */
        TokenHelper::generate(0);
    }

    public function testGenerateUrlSafe(): void
    {
        $token = TokenHelper::generateUrlSafe(32);
        self::assertSame(32, mb_strlen($token));
        self::assertMatchesRegularExpression('/^[A-Za-z0-9_\-]+$/', $token);
    }

    public function testGenerateNumericCode(): void
    {
        $code = TokenHelper::generateNumericCode(6);
        self::assertSame(6, mb_strlen($code));
        self::assertMatchesRegularExpression('/^\d{6}$/', $code);
    }

    public function testGenerateNumericCodeInvalidLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        /** @phpstan-ignore argument.type */
        TokenHelper::generateNumericCode(0);
    }

    public function testUuid(): void
    {
        $uuid = TokenHelper::uuid();
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $uuid
        );
    }

    public function testUuidV7(): void
    {
        $uuid = TokenHelper::uuidV7();
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $uuid
        );
    }

    public function testUlid(): void
    {
        $ulid = TokenHelper::ulid();
        self::assertSame(26, mb_strlen($ulid));
        self::assertMatchesRegularExpression('/^[0-9A-HJKMNP-TV-Z]+$/', $ulid);
    }
}
