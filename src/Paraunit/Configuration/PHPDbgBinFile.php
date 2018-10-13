<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

use Symfony\Component\Process\Process;

/**
 * Class PHPDbgBinFile
 */
class PHPDbgBinFile
{
    /** @var string Realpath to the PHPDbg bin location */
    private $phpDbgBin;

    /**
     * PHPDbgBinFile constructor.
     */
    public function __construct()
    {
        $this->phpDbgBin = $this->getPhpDbgBinLocation();
    }

    /**
     * @throws \RuntimeException When PHPDBG is not available
     *
     * @return string
     */
    public function getPhpDbgBin(): string
    {
        if (! $this->isAvailable()) {
            throw new \RuntimeException('PHPDbg is not available!');
        }

        return $this->phpDbgBin;
    }

    public function isAvailable(): bool
    {
        return $this->phpDbgBin !== '';
    }

    private function getPhpDbgBinLocation(): string
    {
        $checkInPath = new Process('phpdbg --version');
        $checkInPath->run();
        if ($checkInPath->getExitCode() === 0) {
            return 'phpdbg';
        }

        $locator = new Process('command -v phpdbg');
        $locator->run();

        return (string) preg_replace('/\s/', '', $locator->getOutput());
    }
}
