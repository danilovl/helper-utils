<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Misc;

use Throwable;
use InvalidArgumentException;

final class RetryHelper
{
    /**
     * Retries a callback up to $attempts times with exponential backoff.
     *
     * @template T
     * @param callable(): T $callback
     * @param callable(Throwable, int): bool|null $shouldRetry receives ($exception, $attemptNumber); returns true to retry
     * @return T
     * @throws Throwable the last exception if all attempts fail
     */
    public static function retry(
        callable $callback,
        int $attempts = 3,
        int $delayMs = 100,
        float $backoffMultiplier = 2.0,
        ?callable $shouldRetry = null
    ): mixed {
        if ($attempts < 1) {
            throw new InvalidArgumentException('Attempts must be at least 1.');
        }

        $currentDelay = $delayMs;
        $lastException = null;

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                return $callback();
            } catch (Throwable $e) {
                $lastException = $e;
                $isLast = $attempt === $attempts;

                if ($isLast) {
                    break;
                }

                if ($shouldRetry !== null && $shouldRetry($e, $attempt) === false) {
                    break;
                }

                if ($currentDelay > 0) {
                    usleep($currentDelay * 1_000);
                }
                $currentDelay = (int) ($currentDelay * $backoffMultiplier);
            }
        }

        throw $lastException;
    }
}
