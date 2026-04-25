<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Service;

use Danilovl\HelperUtils\Service\MemoryHelper;
use PHPUnit\Framework\TestCase;

final class MemoryHelperTest extends TestCase
{
    private MemoryHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new MemoryHelper;
    }

    public function testGetCurrentUsage(): void
    {
        self::assertGreaterThan(0, $this->helper->getCurrentUsage());
    }

    public function testGetPeakUsage(): void
    {
        self::assertGreaterThan(0, $this->helper->getPeakUsage());
    }

    public function testGetCurrentUsageHuman(): void
    {
        self::assertNotEmpty($this->helper->getCurrentUsageHuman());
    }

    public function testGetMemoryLimit(): void
    {
        $limit = $this->helper->getMemoryLimit();
        self::assertNotSame(0, $limit);
    }

    public function testGetMemoryUsagePercentage(): void
    {
        $percentage = $this->helper->getMemoryUsagePercentage();
        if ($this->helper->getMemoryLimit() > 0) {
            self::assertGreaterThan(0, $percentage);
        } else {
            self::assertSame(0.0, $percentage);
        }
    }
}
