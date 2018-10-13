<?php

declare(strict_types=1);

namespace Tests\Unit\Runner;

use Paraunit\Runner\Pipeline;
use Paraunit\Runner\PipelineCollection;
use Paraunit\Runner\PipelineFactory;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class PipelineCollectionTest
 *
 * @small
 */
class PipelineCollectionTest extends BaseUnitTestCase
{
    public function testInstantiation()
    {
        $pipelines = [
            $this->prophesize(Pipeline::class)->reveal(),
            $this->prophesize(Pipeline::class)->reveal(),
            $this->prophesize(Pipeline::class)->reveal(),
            $this->prophesize(Pipeline::class)->reveal(),
            $this->prophesize(Pipeline::class)->reveal(),
        ];

        new PipelineCollection($this->mockPipelineFactory($pipelines), count($pipelines));
    }

    public function testPush()
    {
        $newProcess = new StubbedParaunitProcess();

        $occupiedPipeline = $this->prophesize(Pipeline::class);
        $occupiedPipeline->isFree()
            ->willReturn(false);
        $occupiedPipeline->execute(Argument::cetera())
            ->shouldNotBeCalled();

        $freePipeline = $this->prophesize(Pipeline::class);
        $freePipeline->isFree()
            ->willReturn(true);
        $freePipeline->execute($newProcess)
            ->shouldBeCalledTimes(1);

        $collection = new PipelineCollection(
            $this->mockPipelineFactory([$occupiedPipeline->reveal(), $freePipeline->reveal()]),
            2
        );

        $collection->push($newProcess);
    }

    public function testPushWithNoEmptyPipelines()
    {
        $newProcess = new StubbedParaunitProcess();

        $occupiedPipeline = $this->prophesize(Pipeline::class);
        $occupiedPipeline->isFree()
            ->willReturn(false);
        $occupiedPipeline->execute(Argument::cetera())
            ->shouldNotBeCalled();

        $collection = new PipelineCollection(
            $this->mockPipelineFactory([$occupiedPipeline->reveal()]),
            1
        );

        $this->expectException(\RuntimeException::class);

        $collection->push($newProcess);
    }

    /**
     * @dataProvider pipelineStateProvider
     *
     * @param bool $isPipeline1Free
     * @param bool $isPipeline2Free
     */
    public function testHasRunningProcesses(bool $isPipeline1Free, bool $isPipeline2Free)
    {
        $pipeline1 = $this->prophesize(Pipeline::class);
        $pipeline1->isFree()
            ->willReturn($isPipeline1Free);
        $pipeline2 = $this->prophesize(Pipeline::class);
        $pipeline2->isFree()
            ->willReturn($isPipeline2Free);

        $pipelineCollection = new PipelineCollection(
            $this->mockPipelineFactory([$pipeline1->reveal(), $pipeline2->reveal()]),
            2
        );

        $expectedResult = $isPipeline1Free && $isPipeline2Free;
        $this->assertSame($expectedResult, $pipelineCollection->isEmpty());
    }

    /**
     * @dataProvider pipelineStateProvider
     *
     * @param bool $isPipeline1Empty
     * @param bool $isPipeline2Empty
     */
    public function testHasEmptySlots(bool $isPipeline1Empty, bool $isPipeline2Empty)
    {
        $pipeline1 = $this->prophesize(Pipeline::class);
        $pipeline1->isFree()
            ->willReturn($isPipeline1Empty);
        $pipeline2 = $this->prophesize(Pipeline::class);
        $pipeline2->isFree()
            ->willReturn($isPipeline2Empty);

        $pipelineCollection = new PipelineCollection(
            $this->mockPipelineFactory([$pipeline1->reveal(), $pipeline2->reveal()]),
            2
        );

        $this->assertSame($isPipeline1Empty || $isPipeline2Empty, $pipelineCollection->hasEmptySlots());
    }

    public function pipelineStateProvider(): array
    {
        return [
            [false, false],
            [false, true],
            [true, false],
            [true, true],
        ];
    }

    /**
     * @param Pipeline[] $pipelines
     *
     * @return PipelineFactory
     */
    private function mockPipelineFactory(array $pipelines): PipelineFactory
    {
        $factory = $this->prophesize(PipelineFactory::class);

        foreach ($pipelines as $number => $pipeline) {
            $factory->create($number + 1)
                ->shouldBeCalledTimes(1)
                ->willReturn($pipeline);
        }

        return $factory->reveal();
    }
}
