<?php

namespace ChrisCollins\GeneralUtils\Phar;

use Phar;
use Symfony\Component\Finder\Finder;

/**
 * PharCompiler
 *
 * Builds a phar file of a project.
 */
class PharCompiler
{
    /**
     * @var array An array of file paths to add to the phar.
     */
    private array $files = [];

    /**
     * Compiles the project into a phar file.
     *
     * @param string $pharOutputPath The path to the phar file that will be created.
     * @param string $binPath The path to the executable file for the stub, i.e. the entry point to the application.
     */
    public function compile(string $pharOutputPath, string $binPath): void
    {
        $pharBaseName = basename($pharOutputPath);

        // Delete the phar file if it already exists.
        if (file_exists($pharOutputPath)) {
            unlink($pharOutputPath);
        }

        $phar = $this->initialisePhar($pharOutputPath, 0, $pharBaseName);
        $phar->startBuffering();

        foreach ($this->files as $file) {
            $this->addFileToPhar($file, $phar);
        }

        $this->addFileToPhar($binPath, $phar);

        $phar->setStub($this->createStub($pharBaseName, $binPath));
        $phar->stopBuffering();
    }

    /**
     * Add a directory.
     *
     * @param string $path The path to the directory.
     *
     * @return static This object.
     */
    public function addDirectory(string $path): self
    {
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->in($path);

        foreach ($finder as $file) {
            $this->addFile($file);
        }

        return $this;
    }

    /**
     * Add a file.
     *
     * @param string $path The path to the file.
     *
     * @return static This object.
     */
    public function addFile($path): self
    {
        $this->files[] = $path;

        return $this;
    }

    /**
     * Accessor method.
     *
     * @return array The value of the property.
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * Add a directory to the phar file.
     *
     * @param string $path The path to the directory.
     */
    private function addFileToPhar($path, Phar $phar): void
    {
        $realPath = realpath($path);

        $content = file_get_contents($realPath);

        $phar->addFromString($realPath, $content);
    }

    /**
     * Create the stub.
     *
     * @param string $pharBaseName The base name of the phar file.
     * @param string $binPath The path to the executable file for the stub, i.e. the entry point to the application.
     *
     * @return string The stub.
     */
    private function createStub($pharBaseName, $binPath): string
    {
        $template = <<<EOF
#!/usr/bin/env php
<?php

Phar::mapPhar('##BASENAME##');

require 'phar://##BASENAME##/##BINPATH##';

__HALT_COMPILER();

EOF;

        return str_replace(['##BASENAME##', '##BINPATH##'], [$pharBaseName, $binPath], $template);
    }

    /**
     * Get a Phar for the given path and base name.
     *
     * @var string $pharOutputPath The path to the phar file that will be created.
     * @var integer $flags Flags for Phar creation.
     * @var string $pharBaseName The basename of the phar file.
     *
     * @return Phar The Phar.
     */
    private function initialisePhar($pharOutputPath, $flags, $pharBaseName): Phar
    {
        return new Phar($pharOutputPath, $flags, $pharBaseName);
    }
}
