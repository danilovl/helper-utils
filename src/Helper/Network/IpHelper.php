<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Network;

use Danilovl\HelperUtils\Exception\InvalidIpException;
use Symfony\Component\HttpFoundation\IpUtils;

final class IpHelper
{
    /**
     * @param string|list<string> $subnets
     */
    public static function isIpInRange(string $ip, string|array $subnets): bool
    {
        return IpUtils::checkIp($ip, $subnets);
    }

    /**
     * @param list<string> $whitelist
     */
    public static function isIpAllowed(string $ip, array $whitelist): bool
    {
        if ($whitelist === []) {
            return false;
        }

        return self::isIpInRange($ip, $whitelist);
    }

    public static function isValid(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    public static function isIpv4(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    public static function isIpv6(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    public static function isPrivate(string $ip): bool
    {
        if (!self::isValid($ip)) {
            return false;
        }

        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }

    public static function isPublic(string $ip): bool
    {
        return self::isValid($ip) && !self::isPrivate($ip) && !self::isLoopback($ip);
    }

    public static function isLoopback(string $ip): bool
    {
        if (!self::isValid($ip)) {
            return false;
        }
        if (self::isIpv4($ip)) {
            return self::isIpInRange($ip, '127.0.0.0/8');
        }

        return $ip === '::1' || mb_strtolower($ip) === '0:0:0:0:0:0:0:1';
    }

    public static function anonymize(string $ip): string
    {
        if (!self::isValid($ip)) {
            throw new InvalidIpException(sprintf('Invalid IP "%s".', $ip));
        }

        return IpUtils::anonymize($ip);
    }

    public static function ipv4ToLong(string $ip): int
    {
        if (!self::isIpv4($ip)) {
            throw new InvalidIpException(sprintf('Not an IPv4 address: "%s".', $ip));
        }

        $long = ip2long($ip);
        if ($long === false) {
            throw new InvalidIpException(sprintf('Cannot convert "%s" to long.', $ip));
        }

        return $long;
    }

    public static function longToIpv4(int $long): string
    {
        return long2ip($long);
    }
}
