<?php

declare(strict_types=1);

namespace ChrisCollins\GeneralUtils\Test;

use PHPUnit\Framework\Attributes\Test;
use ChrisCollins\GeneralUtils\Phar\PharCompiler;

/**
 * PharCompilerTest
 */
final class PharCompilerTest extends AbstractTestCase
{
    /**
     * @var PharCompiler A PharCompiler instance.
     */
    protected $instance;

    /**
     * Set up.
     */
    protected function setUp(): void
    {
        $this->instance = new PharCompiler();
    }

    #[Test]
    public function getFilesReturnsFilesAddedViaAddFile(): void
    {
        $this->assertEmpty($this->instance->getFiles());

        $file = 'C:\test.php';
        $this->instance->addFile($file);

        $files = $this->instance->getFiles();

        $this->assertCount(1, $files);
        $this->assertEquals($file, $files[0]);

        $file2 = 'C:\test2.php';

        $this->instance->addFile($file2);

        $files = $this->instance->getFiles();

        $this->assertCount(2, $files);
        $this->assertEquals($file, $files[0]);
        $this->assertEquals($file2, $files[1]);
    }

    #[Test]
    public function addDirectoryAddsPhpFilesInADirectory(): void
    {
        $this->assertEmpty($this->instance->getFiles());
        $this->instance->addDirectory(__DIR__);

        $files = $this->instance->getFiles();

        $this->assertCount(1, $files);

        $this->assertEquals(__FILE__, $files[0]);
    }
}
