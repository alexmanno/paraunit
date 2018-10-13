<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputPath;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Html\Facade;

/**
 * Class HTMLResult
 */
class Html implements CoverageProcessorInterface
{
    /** @var Facade */
    private $html;

    /** @var OutputPath */
    private $targetPath;

    /**
     * Html constructor.
     *
     * @param OutputPath $targetPath
     */
    public function __construct(OutputPath $targetPath)
    {
        $this->html = new Facade();
        $this->targetPath = $targetPath;
    }

    /**
     * @param CodeCoverage $codeCoverage
     *
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage)
    {
        $this->html->process($codeCoverage, $this->targetPath->getPath());
    }

    public static function getConsoleOptionName(): string
    {
        return 'html';
    }
}
