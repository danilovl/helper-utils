<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Service;

use Danilovl\HelperUtils\Exception\HelperException;
use Danilovl\HelperUtils\Service\TimerHelper;
use PHPUnit\Framework\TestCase;

final class TimerHelperTest extends TestCase
{
    private TimerHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new TimerHelper;
    }

    public function testStartStop(): void
    {
        $this->helper->start('test');
        usleep(1_000); // 1ms
        $duration = $this->helper->stop('test');

        self::assertGreaterThanOrEqual(1, $duration);
    }

    public function testStopThrowsWhenNotStarted(): void
    {
        $this->expectException(HelperException::class);
        $this->helper->stop('missing');
    }

    public function testMeasure(): void
    {
        $data = $this->helper->measure(static function () {
            usleep(1_000);

            return 'result';
        });

        self::assertSame('result', $data['result']);
        self::assertGreaterThanOrEqual(1, $data['duration_ms']);
    }

    public function testReset(): void
    {
        $this->helper->start('test');
        $this->helper->reset();

        $this->expectException(HelperException::class);
        $this->helper->stop('test');
    }
}
