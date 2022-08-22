<?php

namespace ChrisCollins\GeneralUtils\Test;

use ChrisCollins\GeneralUtils\Phar\PharCompiler;

/**
 * PharCompilerTest
 */
class PharCompilerTest extends AbstractTestCase
{
    /**
     * @var PharCompiler A PharCompiler instance.
     */
    protected $instance = null;

    /**
     * Set up.
     */
    public function setUp(): void
    {
        $this->instance = new PharCompiler();
    }

    public function testGetFilesReturnsFilesAddedViaAddFile(): void
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

    public function testAddDirectoryAddsPhpFilesInADirectory(): void
    {
        $this->assertEmpty($this->instance->getFiles());
        $this->instance->addDirectory(__DIR__);

        $files = $this->instance->getFiles();

        $this->assertCount(1, $files);

        $this->assertEquals(__FILE__, $files[0]);
    }
}
