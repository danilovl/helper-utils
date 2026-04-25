<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\Hash;

use Danilovl\HelperUtils\Exception\HelperException;
use Danilovl\HelperUtils\Helper\Hash\HashHelper;
use PHPUnit\Framework\TestCase;

final class HashHelperTest extends TestCase
{
    public function testMd5(): void
    {
        self::assertSame(md5('hello'), HashHelper::md5('hello'));
    }

    public function testSha256(): void
    {
        self::assertSame(hash('sha256', 'hello'), HashHelper::sha256('hello'));
    }

    public function testSha512(): void
    {
        self::assertSame(hash('sha512', 'hello'), HashHelper::sha512('hello'));
    }

    public function testHashCustomAlgorithm(): void
    {
        self::assertSame(hash('crc32', 'hello'), HashHelper::hash('hello', 'crc32'));
    }

    public function testHashUnknownAlgorithm(): void
    {
        $this->expectException(HelperException::class);
        HashHelper::hash('hello', 'unknown_algo_xyz');
    }

    public function testFileHash(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'hash');
        file_put_contents($tmp, 'content');

        try {
            self::assertSame(hash_file('sha256', $tmp), HashHelper::fileHash($tmp));
        } finally {
            unlink($tmp);
        }
    }

    public function testFileHashNonExistent(): void
    {
        $this->expectException(HelperException::class);
        HashHelper::fileHash('/nonexistent/path');
    }

    public function testSafeEqualsTrue(): void
    {
        self::assertTrue(HashHelper::safeEquals('hello', 'hello'));
    }

    public function testSafeEqualsFalse(): void
    {
        self::assertFalse(HashHelper::safeEquals('hello', 'world'));
    }
}
