<?php

declare(strict_types=1);

namespace Paraunit\TestResult\Interfaces;

use Paraunit\TestResult\TestResultFormat;

/**
 * Interface PrintableTestResultInterface
 */
interface PrintableTestResultInterface extends TestResultInterface
{
    public function getTestResultFormat(): TestResultFormat;

    public function setTestResultFormat(TestResultFormat $testResultFormat);
}
