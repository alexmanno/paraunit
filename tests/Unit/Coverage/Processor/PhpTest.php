<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use Paraunit\Coverage\Processor\Php;
use Tests\BaseUnitTestCase;

/**
 * Class PhpTest
 */
class PhpTest extends BaseUnitTestCase
{
    public function testWriteToFile()
    {
        $targetFile = new OutputFile(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'coverage.php');
        $text = new Php($targetFile);

        $this->assertFileNotExists($targetFile->getFilePath());

        $text->process($this->createCodeCoverage());

        $this->assertFileExists($targetFile->getFilePath());
        $content = file_get_contents($targetFile->getFilePath());
        unlink($targetFile->getFilePath());
        $this->assertStringStartsWith('<?php', $content);
        $this->assertContains('$coverage = new', $content);
        $this->assertContains('return $coverage;', $content);
    }
}
