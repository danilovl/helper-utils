<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Service;

use DateTimeImmutable;
use Danilovl\HelperUtils\Service\ClockAwareDateHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

#[CoversClass(ClockAwareDateHelper::class)]
final class ClockAwareDateHelperTest extends TestCase
{
    private ClockInterface $clock;

    private ClockAwareDateHelper $helper;

    private DateTimeImmutable $now;

    protected function setUp(): void
    {
        $this->now = new DateTimeImmutable('2026-04-25 12:00:00');
        $this->clock = $this->createStub(ClockInterface::class);
        $this->clock->method('now')->willReturn($this->now);

        $this->helper = new ClockAwareDateHelper($this->clock);
    }

    public function testNow(): void
    {
        self::assertSame($this->now, $this->helper->now());
    }

    public function testToday(): void
    {
        $today = $this->helper->today();
        self::assertSame('2026-04-25 00:00:00', $today->format('Y-m-d H:i:s'));
    }

    public function testTomorrow(): void
    {
        $tomorrow = $this->helper->tomorrow();
        self::assertSame('2026-04-26 00:00:00', $tomorrow->format('Y-m-d H:i:s'));
    }

    public function testYesterday(): void
    {
        $yesterday = $this->helper->yesterday();
        self::assertSame('2026-04-24 00:00:00', $yesterday->format('Y-m-d H:i:s'));
    }

    public function testIsExpired(): void
    {
        self::assertFalse($this->helper->isExpired(null));
        self::assertTrue($this->helper->isExpired($this->now->modify('-1 second')));
        self::assertFalse($this->helper->isExpired($this->now->modify('+1 second')));
        self::assertFalse($this->helper->isExpired($this->now));
    }
}
