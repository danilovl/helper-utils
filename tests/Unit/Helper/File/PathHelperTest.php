<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\File;

use Danilovl\HelperUtils\Helper\File\PathHelper;
use PHPUnit\Framework\TestCase;

final class PathHelperTest extends TestCase
{
    public function testJoin(): void
    {
        self::assertSame('a/b/c', PathHelper::join('a', 'b', 'c'));
        self::assertSame('a/b/c', PathHelper::join('a/', '/b/', '/c'));
        self::assertSame('/a/b/c', PathHelper::join('/a', 'b', 'c'));
    }

    public function testJoinEmptySegments(): void
    {
        self::assertSame('a/b', PathHelper::join('a', '', 'b'));
    }

    public function testNormalize(): void
    {
        self::assertSame('a/b/c', PathHelper::normalize('a/./b/../b/c'));
        self::assertSame('/a/b', PathHelper::normalize('/a//b'));
    }

    public function testIsAbsolute(): void
    {
        self::assertTrue(PathHelper::isAbsolute('/usr/local'));
        self::assertFalse(PathHelper::isAbsolute('relative/path'));
    }

    public function testMakeAbsolute(): void
    {
        self::assertSame('/base/sub/file', PathHelper::makeAbsolute('sub/file', '/base'));
    }

    public function testMakeRelative(): void
    {
        self::assertSame('sub/file', PathHelper::makeRelative('/base/sub/file', '/base'));
    }

    public function testIsWithin(): void
    {
        self::assertTrue(PathHelper::isWithin('/var/www/html/file.txt', '/var/www'));
        self::assertTrue(PathHelper::isWithin('/var/www', '/var/www'));
        self::assertFalse(PathHelper::isWithin('/var/www2/file.txt', '/var/www'));
        self::assertFalse(PathHelper::isWithin('/etc/passwd', '/var/www'));
    }

    public function testIsWithinTraversal(): void
    {
        self::assertFalse(PathHelper::isWithin('/var/www/../../etc/passwd', '/var/www'));
    }
}
