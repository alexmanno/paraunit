<?php

namespace Paraunit\Configuration;

use Paraunit\File\TempDirectory;

/**
 * Class TempFilenameFactory
 * @package Tests\Unit\Parser
 */
class TempFilenameFactory
{
    /** @var  TempDirectory */
    private $tempDirectory;

    /**
     * TempFilenameFactory constructor.
     * @param TempDirectory $tempDirectory
     */
    public function __construct(TempDirectory $tempDirectory)
    {
        $this->tempDirectory = $tempDirectory;
    }

    /**
     * @param string $uniqueId
     * @return string
     */
    public function getFilenameForLog($uniqueId)
    {
        return $this->tempDirectory->getTempDirForThisExecution()
            . '/logs/'
            . $uniqueId
            . '.json.log';
    }

    /**
     * @param string $uniqueId
     * @return string
     */
    public function getFilenameForCoverage($uniqueId)
    {
        return $this->tempDirectory->getTempDirForThisExecution()
            . '/coverage/'
            . $uniqueId
            . '.php';
    }
}