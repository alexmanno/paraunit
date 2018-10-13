<?php

declare(strict_types=1);

namespace Paraunit\Coverage;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\Proxy\Coverage\FakeDriver;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use Symfony\Component\Process\Process;

/**
 * Class CoverageFetcher
 */
class CoverageFetcher
{
    /** @var TempFilenameFactory */
    private $tempFilenameFactory;

    /** @var TestResultHandlerInterface */
    private $resultHandler;

    /**
     * CoverageFetcher constructor.
     *
     * @param TempFilenameFactory $tempFilenameFactory
     * @param TestResultHandlerInterface $failureHandler
     */
    public function __construct(TempFilenameFactory $tempFilenameFactory, TestResultHandlerInterface $failureHandler)
    {
        $this->tempFilenameFactory = $tempFilenameFactory;
        $this->resultHandler = $failureHandler;
    }

    public function fetch(AbstractParaunitProcess $process): CodeCoverage
    {
        $tempFilename = $this->tempFilenameFactory->getFilenameForCoverage($process->getUniqueId());
        $codeCoverage = null;

        if ($this->coverageFileIsValid($tempFilename)) {
            $codeCoverage = require $tempFilename;
            unlink($tempFilename);
        }

        if ($codeCoverage instanceof CodeCoverage) {
            return $codeCoverage;
        }

        $this->resultHandler->addProcessToFilenames($process);

        return new CodeCoverage(new FakeDriver());
    }

    private function coverageFileIsValid(string $tempFilename): bool
    {
        if (! file_exists($tempFilename)) {
            return false;
        }

        try {
            $this->overrideCoverageClassDefinition($tempFilename);

            $verificationProcess = new Process('php --syntax-check ' . $tempFilename);
            $verificationProcess->run();

            return $verificationProcess->getExitCode() === 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param string $tempFilename
     */
    private function overrideCoverageClassDefinition($tempFilename)
    {
        $fileContent = str_replace(
            [
                'new SebastianBergmann\CodeCoverage\CodeCoverage;',
                'new SebastianBergmann\CodeCoverage\CodeCoverage();',
                'new CodeCoverage;',
                'new CodeCoverage();',
            ],
            'new SebastianBergmann\CodeCoverage\CodeCoverage(new ' . FakeDriver::class . ');',
            file_get_contents($tempFilename)
        );

        file_put_contents($tempFilename, $fileContent);
    }
}
