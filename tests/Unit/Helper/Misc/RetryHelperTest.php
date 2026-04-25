<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Misc;

use Danilovl\HelperUtils\Helper\Misc\RetryHelper;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use RuntimeException;

final class RetryHelperTest extends TestCase
{
    public function testReturnsResultOnFirstSuccess(): void
    {
        $result = RetryHelper::retry(static fn (): int => 42);
        self::assertSame(42, $result);
    }

    public function testRetriesUntilSuccess(): void
    {
        $attempts = 0;
        $result = RetryHelper::retry(static function () use (&$attempts): string {
            $attempts++;
            if ($attempts < 3) {
                throw new RuntimeException('fail');
            }

            return 'ok';
        }, 5, 1, 1.0);

        self::assertSame('ok', $result);
        self::assertSame(3, $attempts);
    }

    public function testThrowsAfterAllAttempts(): void
    {
        $attempts = 0;

        try {
            RetryHelper::retry(static function () use (&$attempts): void {
                $attempts++;

                throw new RuntimeException('always fails');
            }, 3, 1, 1.0);
            /** @phpstan-ignore deadCode.unreachable */
            self::fail('Expected exception not thrown.');
        } catch (RuntimeException $e) {
            self::assertSame('always fails', $e->getMessage());
            self::assertSame(3, $attempts);
        }
    }

    public function testShouldRetryFalseStops(): void
    {
        $attempts = 0;

        try {
            RetryHelper::retry(
                static function () use (&$attempts): never {
                    $attempts++;

                    throw new RuntimeException('fail');
                },
                attempts: 5,
                delayMs: 1,
                shouldRetry: static fn (): bool => false
            );
        } catch (RuntimeException) {
            // expected
        }
        self::assertSame(1, $attempts);
    }

    public function testInvalidAttempts(): void
    {
        $this->expectException(InvalidArgumentException::class);
        RetryHelper::retry(static fn () => 1, 0);
    }
}
