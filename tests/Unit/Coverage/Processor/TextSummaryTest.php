<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use Paraunit\Coverage\Processor\TextSummary;
use Prophecy\Argument;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\BaseUnitTestCase;

/**
 * Class TextSummaryTest
 */
class TextSummaryTest extends BaseUnitTestCase
{
    /**
     * @dataProvider colorProvider
     */
    public function testWriteToFile(bool $withColors, string $expectedString)
    {
        $targetFile = new OutputFile(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'coverage.txt');
        $text = new TextSummary(
            $this->prophesize(OutputInterface::class)->reveal(),
            $withColors,
            $targetFile
        );

        $this->assertFileNotExists($targetFile->getFilePath());

        $text->process($this->createCodeCoverage());

        $this->assertFileExists($targetFile->getFilePath());
        $content = file_get_contents($targetFile->getFilePath());
        unlink($targetFile->getFilePath());
        $this->assertContains($expectedString, $content);
    }

    /**
     * @dataProvider colorProvider
     */
    public function testWriteToOutput(bool $withColors, string $expectedString)
    {
        $output = $this->prophesize(OutputInterface::class);
        $output->writeln(Argument::containingString($expectedString))
            ->shouldBeCalledTimes(1)
            ->willReturn();
        $text = new TextSummary($output->reveal(), $withColors);

        $text->process($this->createCodeCoverage());
    }

    public function colorProvider()
    {
        return [
            [false, 'Code Coverage Report Summary:'],
            [true, "\x1b[1;37;40mCode Coverage Report Summary:"],
        ];
    }
}
