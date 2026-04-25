<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Service;

use Danilovl\HelperUtils\Helper\Number\NumberHelper;

final class MemoryHelper
{
    public function getCurrentUsage(bool $real = false): int
    {
        return memory_get_usage($real);
    }

    public function getPeakUsage(bool $real = false): int
    {
        return memory_get_peak_usage($real);
    }

    public function getCurrentUsageHuman(): string
    {
        return NumberHelper::formatBytes($this->getCurrentUsage());
    }

    public function getMemoryLimit(): int
    {
        $limit = ini_get('memory_limit');
        /** @phpstan-ignore function.alreadyNarrowedType */
        if (!is_string($limit) || $limit === '' || $limit === '-1') {
            return -1;
        }

        return $this->parseLimit($limit);
    }

    public function getMemoryUsagePercentage(): float
    {
        $limit = $this->getMemoryLimit();
        if ($limit <= 0) {
            return 0.0;
        }

        return ($this->getCurrentUsage() / $limit) * 100;
    }

    private function parseLimit(string $limit): int
    {
        $limit = mb_trim($limit);
        $unit = mb_strtoupper(mb_substr($limit, -1));
        $value = (int) $limit;

        return match ($unit) {
            'G' => $value * 1_024 ** 3,
            'M' => $value * 1_024 ** 2,
            'K' => $value * 1_024,
            default => $value,
        };
    }
}
