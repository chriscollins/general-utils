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
    public function setUp()
    {
        $this->instance = new PharCompiler();
    }

    public function testGetFilesReturnsFilesAddedViaAddFile()
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

    public function testAddDirectoryAddsPhpFilesInADirectory()
    {
        $this->assertEmpty($this->instance->getFiles());
        $this->instance->addDirectory(__DIR__);

        $files = $this->instance->getFiles();

        $this->assertCount(1, $files);

        $this->assertEquals(__FILE__, $files[0]);
    }

    public function testCompileCreatesAPharObjectAndCallsExpectedMethodsOnIt()
    {
        $phar = $this->getMock('Phar', array(), array('C:\test.phar'), '', true);

        $phar->expects($this->exactly(2))
            ->method('addFromString')
            ->with($this->equalTo(__FILE__));

        $phar->expects($this->once())
            ->method('setStub');

        $compiler = $this->getMockBuilder('ChrisCollins\GeneralUtils\Phar\PharCompiler')
            ->setMethods(array('initialisePhar'))
            ->getMock();

        $compiler->expects($this->once())
            ->method('initialisePhar')
            ->will($this->returnValue($phar));

        $compiler->addDirectory(__DIR__);

        $compiler->compile(__DIR__ . DIRECTORY_SEPARATOR . 'test.phar', __FILE__);
    }
}
