<?php

declare(strict_types=1);

namespace Tests\Stub\PHPUnitJSONLogOutput;

/**
 * Class JSONLogStub
 */
class JSONLogStub
{
    const TWO_ERRORS_TWO_FAILURES = '2Errors2Failures';

    const ALL_GREEN = 'AllGreen';

    const FATAL_ERROR = 'FatalError';

    const SEGFAULT = 'SegFault';

    const ONE_ERROR = 'SingleError';

    const ONE_INCOMPLETE = 'SingleIncomplete';

    const ONE_RISKY = 'SingleRisky';

    const ONE_SKIP = 'SingleSkip';

    const ONE_WARNING = 'SingleWarning';

    const UNKNOWN = 'Unknown';

    const PARSE_ERROR = 'ParseError';

    /**
     * @param string $filename
     *
     * @throws \Exception
     *
     * @return string
     */
    public static function getLogs(string $filename): string
    {
        return json_decode(self::getCleanOutputFileContent($filename));
    }

    /**
     * @param string $filename
     *
     * @throws \Exception
     *
     * @return string
     */
    public static function getCleanOutputFileContent(string $filename): string
    {
        $fullFilename = __DIR__ . DIRECTORY_SEPARATOR . $filename . '.json';
        if (! file_exists($fullFilename)) {
            throw new \Exception('Unknown file stub: ' . $filename);
        }

        return self::cleanLog(file_get_contents($fullFilename));
    }

    /**
     * @param string $jsonString The dirty output
     *
     * @return string            The normalized log, as an array of JSON objects
     */
    private static function cleanLog($jsonString)
    {
        $splitted = preg_replace('/\}\{/', '},{', $jsonString);

        return '[' . $splitted . ']';
    }
}
