<?php

declare(strict_types=1);

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
     * @var string[] An array of file paths to add to the phar.
     */
    private array $files = [];

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

    public function addDirectory(string $path): self
    {
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->in($path);

        foreach ($finder as $file) {
            $this->addFile((string) $file);
        }

        return $this;
    }

    public function addFile(string $path): self
    {
        $this->files[] = $path;

        return $this;
    }

    /**
     * Accessor method.
     *
     * @return string[] The value of the property.
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    private function addFileToPhar(string $path, Phar $phar): void
    {
        $realPath = realpath($path);

        $content = file_get_contents($realPath);

        $phar->addFromString($realPath, $content);
    }

    private function createStub(string $pharBaseName, string $binPath): string
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

    private function initialisePhar(string $pharOutputPath, int $flags, string $pharBaseName): Phar
    {
        return new Phar($pharOutputPath, $flags, $pharBaseName);
    }
}
