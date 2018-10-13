<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Text as PHPUnitText;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractText implements CoverageProcessorInterface
{
    /** @var PHPUnitText */
    private $text;

    /** @var OutputInterface */
    private $output;

    /** @var OutputFile */
    private $targetFile;

    /** @var bool */
    private $showColors;

    /**
     * AbstractText constructor.
     *
     * @param OutputInterface $output
     * @param bool $showColors
     * @param bool $onlySummary
     * @param OutputFile|null $targetFile
     */
    public function __construct(OutputInterface $output, bool $showColors, bool $onlySummary, OutputFile $targetFile = null)
    {
        $this->text = new PHPUnitText(50, 90, false, $onlySummary);
        $this->output = $output;
        $this->targetFile = $targetFile;
        $this->showColors = $showColors;
    }

    /**
     * @param CodeCoverage $coverage
     *
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $coverage)
    {
        $coverageResults = $this->text->process($coverage, $this->showColors);

        if ($this->targetFile) {
            file_put_contents($this->targetFile->getFilePath(), $coverageResults);
        } else {
            $this->output->writeln($coverageResults);
        }
    }
}
