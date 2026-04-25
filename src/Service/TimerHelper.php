<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Service;

use Danilovl\HelperUtils\Exception\HelperException;

final class TimerHelper
{
    /** @var array<string, float> */
    private array $timers = [];

    public function start(string $name): void
    {
        $this->timers[$name] = microtime(true);
    }

    public function stop(string $name): float
    {
        if (!isset($this->timers[$name])) {
            throw new HelperException(sprintf('Timer "%s" was not started.', $name));
        }
        $duration = (microtime(true) - $this->timers[$name]) * 1_000;
        unset($this->timers[$name]);

        return $duration;
    }

    /**
     * @template T
     * @param callable(): T $callback
     * @return array{result: T, duration_ms: float}
     */
    public function measure(callable $callback, ?string $name = null): array
    {
        $name ??= 'measure_' . uniqid();
        $this->start($name);
        $result = $callback();
        $duration = $this->stop($name);

        return ['result' => $result, 'duration_ms' => $duration];
    }

    public function reset(): void
    {
        $this->timers = [];
    }
}
