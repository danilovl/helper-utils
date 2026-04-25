<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Helper\File;

use Danilovl\HelperUtils\Helper\File\FileHelper;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

final class FileHelperTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/helper-utils-' . uniqid();
        mkdir($this->tmpDir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tmpDir)) {
            FileHelper::deleteDirectory($this->tmpDir);
        }
    }

    public function testCreateTmpFileEmpty(): void
    {
        $path = FileHelper::createTmpFile();
        self::assertFileExists($path);
        unlink($path);
    }

    public function testCreateTmpFileWithContentAndExtension(): void
    {
        $path = FileHelper::createTmpFile('hello', 'txt');
        self::assertFileExists($path);
        self::assertSame('hello', file_get_contents($path));
        self::assertSame('txt', pathinfo($path, PATHINFO_EXTENSION));
        unlink($path);
    }

    public function testEnsureDirectory(): void
    {
        $path = $this->tmpDir . '/a/b/c';
        FileHelper::ensureDirectory($path);
        self::assertDirectoryExists($path);
    }

    public function testDeleteDirectory(): void
    {
        $path = $this->tmpDir . '/sub';
        mkdir($path);
        file_put_contents($path . '/file.txt', 'content');
        FileHelper::deleteDirectory($path);
        self::assertDirectoryDoesNotExist($path);
    }

    public function testDeleteDirectoryNonExistent(): void
    {
        FileHelper::deleteDirectory($this->tmpDir . '/nonexistent');
        $this->expectNotToPerformAssertions();
    }

    public function testCopyDirectory(): void
    {
        $src = $this->tmpDir . '/src';
        $dst = $this->tmpDir . '/dst';
        mkdir($src);
        file_put_contents($src . '/file.txt', 'hello');
        FileHelper::copyDirectory($src, $dst);
        self::assertFileExists($dst . '/file.txt');
        self::assertSame('hello', file_get_contents($dst . '/file.txt'));
    }

    public function testGetExtension(): void
    {
        self::assertSame('pdf', FileHelper::getExtension('report.pdf'));
        self::assertSame('gz', FileHelper::getExtension('archive.tar.gz'));
        self::assertSame('', FileHelper::getExtension('noext'));
    }

    public function testGetBasenameWithoutExtension(): void
    {
        self::assertSame('report', FileHelper::getBasenameWithoutExtension('report.pdf'));
        self::assertSame('archive.tar', FileHelper::getBasenameWithoutExtension('archive.tar.gz'));
    }

    public function testSanitizeFilename(): void
    {
        self::assertSame('hello_world.txt', FileHelper::sanitizeFilename('hello/world.txt'));
        self::assertSame('file_with_chars', FileHelper::sanitizeFilename('file<with>chars'));
        self::assertSame('file', FileHelper::sanitizeFilename('....'));
    }

    public function testGenerateUniqueFilename(): void
    {
        file_put_contents($this->tmpDir . '/file.txt', '');
        $unique = FileHelper::generateUniqueFilename($this->tmpDir, 'file.txt');
        self::assertSame('file_1.txt', $unique);

        file_put_contents($this->tmpDir . '/file_1.txt', '');
        $unique = FileHelper::generateUniqueFilename($this->tmpDir, 'file.txt');
        self::assertSame('file_2.txt', $unique);
    }

    public function testGenerateUniqueFilenameNoConflict(): void
    {
        self::assertSame('file.txt', FileHelper::generateUniqueFilename($this->tmpDir, 'file.txt'));
    }

    public function testHumanReadableSize(): void
    {
        self::assertSame('500.00 B', FileHelper::humanReadableSize(500));
        self::assertSame('1.00 KB', FileHelper::humanReadableSize(1_024));
        self::assertSame('1.50 MB', FileHelper::humanReadableSize(1_572_864));
    }

    public function testParseSize(): void
    {
        self::assertSame(1_024, FileHelper::parseSize('1KB'));
        self::assertSame(1_024, FileHelper::parseSize('1K'));
        self::assertSame(1_048_576, FileHelper::parseSize('1MB'));
        self::assertSame(1_572_864, FileHelper::parseSize('1.5MB'));
        self::assertSame(500, FileHelper::parseSize('500'));
        self::assertSame(500, FileHelper::parseSize('500B'));
    }

    public function testParseSizeInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        FileHelper::parseSize('not a size');
    }

    public function testIsHidden(): void
    {
        self::assertTrue(FileHelper::isHidden('/path/.hidden'));
        self::assertFalse(FileHelper::isHidden('/path/visible.txt'));
    }

    public function testFileHash(): void
    {
        $path = FileHelper::createTmpFile('content');
        $hash = FileHelper::fileHash($path);
        self::assertSame(hash('sha256', 'content'), $hash);
        unlink($path);
    }
}
