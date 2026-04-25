<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Service;

use DateTimeImmutable;
use Danilovl\HelperUtils\Helper\Date\DateHelper;
use Psr\Clock\ClockInterface;

final readonly class ClockAwareDateHelper
{
    public function __construct(private ClockInterface $clock) {}

    public function now(): DateTimeImmutable
    {
        return $this->clock->now();
    }

    public function today(): DateTimeImmutable
    {
        return DateHelper::startOfDay($this->now());
    }

    public function tomorrow(): DateTimeImmutable
    {
        return $this->today()->modify('+1 day');
    }

    public function yesterday(): DateTimeImmutable
    {
        return $this->today()->modify('-1 day');
    }

    public function isExpired(?DateTimeImmutable $expiresAt): bool
    {
        if ($expiresAt === null) {
            return false;
        }

        return $expiresAt < $this->now();
    }
}
