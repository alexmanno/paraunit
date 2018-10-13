<?php

declare(strict_types=1);

namespace Tests\Unit\TestResult;

use Paraunit\Parser\JSON\LogFetcher;
use Paraunit\TestResult\FullTestResult;
use Paraunit\TestResult\MuteTestResult;
use Paraunit\TestResult\TestResultFactory;
use Paraunit\TestResult\TestResultFormat;
use Paraunit\TestResult\TestResultWithAbnormalTermination;
use Paraunit\TestResult\TestResultWithMessage;
use Tests\BaseUnitTestCase;

/**
 * Class TestResultFactoryTest
 */
class TestResultFactoryTest extends BaseUnitTestCase
{
    public function testCreateFromLogMuteWithoutTestName()
    {
        $log = new \stdClass();
        $log->event = 'test';

        $factory = new TestResultFactory();
        $result = $factory->createFromLog($log);

        $this->assertInstanceOf(MuteTestResult::class, $result);
        $this->assertInstanceOf(TestResultFormat::class, $result->getTestResultFormat());
    }

    public function testCreateFromLogMute()
    {
        $log = new \stdClass();
        $log->test = 'testFunction()';
        $log->event = 'test';

        $factory = new TestResultFactory();
        $result = $factory->createFromLog($log);

        $this->assertInstanceOf(MuteTestResult::class, $result);
        $this->assertInstanceOf(TestResultFormat::class, $result->getTestResultFormat());
    }

    public function testCreateFromLogWithMessage()
    {
        $log = $this->getLogFromStub('test', 'error');
        unset($log->trace);

        $factory = new TestResultFactory();
        /** @var TestResultWithMessage $result */
        $result = $factory->createFromLog($log);

        $this->assertInstanceOf(TestResultWithMessage::class, $result);
        $this->assertInstanceOf(TestResultFormat::class, $result->getTestResultFormat());
        $this->assertEquals($log->test, $result->getFunctionName());
    }

    public function testCreateFromLogWithTrace()
    {
        $log = $this->getLogWithTrace();

        $factory = new TestResultFactory();
        /** @var FullTestResult $result */
        $result = $factory->createFromLog($log);

        $this->assertInstanceOf(FullTestResult::class, $result);
        $this->assertInstanceOf(TestResultFormat::class, $result->getTestResultFormat());
        $this->assertEquals($log->message, $result->getFailureMessage());
        $this->assertSame($log->trace, $result->getTrace());
    }

    public function testCreateFromLogWithAbnormalTermination()
    {
        $log = $this->getLogFromStub();
        $log->status = LogFetcher::LOG_ENDING_STATUS;
        $log->test = 'testFunction()';

        $factory = new TestResultFactory();
        /** @var TestResultWithAbnormalTermination $result */
        $result = $factory->createFromLog($log);

        $this->assertInstanceOf(TestResultWithAbnormalTermination::class, $result);
        $this->assertInstanceOf(TestResultFormat::class, $result->getTestResultFormat());
        // TestStartParser injects the last launched test function name
        $this->assertEquals($log->test, $result->getFunctionName());
        $this->assertStringStartsWith('Abnormal termination -- complete test output:', $result->getFailureMessage());
    }
}
