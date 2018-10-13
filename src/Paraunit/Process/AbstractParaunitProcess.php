<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Paraunit\TestResult\TestResultWithAbnormalTermination;

/**
 * Class AbstractParaunitProcess
 */
abstract class AbstractParaunitProcess
{
    /** @var int */
    protected $retryCount = 0;

    /** @var bool */
    protected $shouldBeRetried;

    /** @var string */
    protected $uniqueId;

    /** @var string */
    protected $filename;

    /** @var string */
    protected $testClassName;

    /** @var PrintableTestResultInterface[] */
    protected $testResults;

    /** @var bool */
    private $waitingForTestResult;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
        $this->uniqueId = md5($this->filename);
        $this->testResults = [];
        $this->waitingForTestResult = true;
        $this->shouldBeRetried = false;
    }

    abstract public function getOutput(): string;

    abstract public function isTerminated(): bool;

    abstract public function getCommandLine(): string;

    /**
     * @return int|null
     */
    abstract public function getExitCode();

    /**
     * @param int $pipelineNumber
     */
    abstract public function start(int $pipelineNumber);

    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }

    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    public function increaseRetryCount()
    {
        ++$this->retryCount;
    }

    public function markAsToBeRetried()
    {
        $this->reset();
        $this->increaseRetryCount();
        $this->shouldBeRetried = true;
    }

    public function isToBeRetried(): bool
    {
        return $this->shouldBeRetried;
    }

    public function reset()
    {
        $this->shouldBeRetried = false;
        $this->testResults = [];
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return string|null
     */
    public function getTestClassName()
    {
        return $this->testClassName;
    }

    public function setTestClassName(string $testClassName)
    {
        $this->testClassName = $testClassName;
    }

    /**
     * @return PrintableTestResultInterface[]
     */
    public function getTestResults(): array
    {
        return $this->testResults;
    }

    public function addTestResult(PrintableTestResultInterface $testResult)
    {
        $this->testResults[] = $testResult;
        $this->waitingForTestResult = false;
    }

    public function hasAbnormalTermination(): bool
    {
        return end($this->testResults) instanceof TestResultWithAbnormalTermination;
    }

    public function isWaitingForTestResult(): bool
    {
        return $this->waitingForTestResult;
    }

    public function setWaitingForTestResult(bool $waitingForTestResult)
    {
        $this->waitingForTestResult = $waitingForTestResult;
    }
}
