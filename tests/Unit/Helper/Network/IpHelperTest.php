<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Network;

use Danilovl\HelperUtils\Exception\InvalidIpException;
use Danilovl\HelperUtils\Helper\Network\IpHelper;
use PHPUnit\Framework\TestCase;

final class IpHelperTest extends TestCase
{
    public function testIsValid(): void
    {
        self::assertTrue(IpHelper::isValid('192.168.1.1'));
        self::assertTrue(IpHelper::isValid('::1'));
        self::assertTrue(IpHelper::isValid('2001:db8::1'));
        self::assertFalse(IpHelper::isValid('not.an.ip'));
        self::assertFalse(IpHelper::isValid('256.0.0.0'));
    }

    public function testIsIpv4(): void
    {
        self::assertTrue(IpHelper::isIpv4('192.168.1.1'));
        self::assertFalse(IpHelper::isIpv4('::1'));
    }

    public function testIsIpv6(): void
    {
        self::assertTrue(IpHelper::isIpv6('::1'));
        self::assertFalse(IpHelper::isIpv6('192.168.1.1'));
    }

    public function testIsPrivate(): void
    {
        self::assertTrue(IpHelper::isPrivate('192.168.1.1'));
        self::assertTrue(IpHelper::isPrivate('10.0.0.1'));
        self::assertTrue(IpHelper::isPrivate('172.16.0.1'));
        self::assertFalse(IpHelper::isPrivate('8.8.8.8'));
    }

    public function testIsPublic(): void
    {
        self::assertTrue(IpHelper::isPublic('8.8.8.8'));
        self::assertFalse(IpHelper::isPublic('192.168.1.1'));
        self::assertFalse(IpHelper::isPublic('127.0.0.1'));
    }

    public function testIsLoopback(): void
    {
        self::assertTrue(IpHelper::isLoopback('127.0.0.1'));
        self::assertTrue(IpHelper::isLoopback('127.255.0.1'));
        self::assertTrue(IpHelper::isLoopback('::1'));
        self::assertFalse(IpHelper::isLoopback('192.168.1.1'));
    }

    public function testIsIpInRange(): void
    {
        self::assertTrue(IpHelper::isIpInRange('192.168.1.5', '192.168.1.0/24'));
        self::assertFalse(IpHelper::isIpInRange('10.0.0.1', '192.168.1.0/24'));
    }

    public function testIsIpInRangeMultiple(): void
    {
        self::assertTrue(IpHelper::isIpInRange('10.0.0.1', ['192.168.1.0/24', '10.0.0.0/8']));
    }

    public function testIsIpAllowed(): void
    {
        self::assertTrue(IpHelper::isIpAllowed('192.168.1.5', ['192.168.1.0/24']));
        self::assertFalse(IpHelper::isIpAllowed('10.0.0.1', ['192.168.1.0/24']));
        self::assertFalse(IpHelper::isIpAllowed('10.0.0.1', []));
    }

    public function testAnonymize(): void
    {
        self::assertSame('192.168.1.0', IpHelper::anonymize('192.168.1.123'));
    }

    public function testAnonymizeInvalid(): void
    {
        $this->expectException(InvalidIpException::class);
        IpHelper::anonymize('not an ip');
    }

    public function testIpv4ToLong(): void
    {
        self::assertSame(ip2long('192.168.1.1'), IpHelper::ipv4ToLong('192.168.1.1'));
    }

    public function testIpv4ToLongInvalid(): void
    {
        $this->expectException(InvalidIpException::class);
        IpHelper::ipv4ToLong('::1');
    }

    public function testLongToIpv4(): void
    {
        $long = ip2long('192.168.1.1');
        self::assertIsInt($long);
        self::assertSame('192.168.1.1', IpHelper::longToIpv4($long));
    }
}
