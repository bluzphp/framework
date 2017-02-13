<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests;

use Bluz\Cli\Colorize;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener as PHPUnitTestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;

/**
 * Test Listener for format output
 *
 * @package  Bluz\Tests
 *
 * @author   Anton Shevchuk
 */
class TestListener implements PHPUnitTestListener
{
    /**
     * time of suite
     *
     * @var integer
     */
    protected $timeSuite = 0;
    
    /**
     * @param Test $test
     * @param \Exception $e
     * @param float $time
     * @return void
     */
    public function addError(Test $test, \Exception $e, $time)
    {
        echo "\t[";
        echo Colorize::text($e->getMessage(), "red", null, true);
        echo "] ";
    }

    /**
     * A warning occurred.
     *
     * @param Test  $test
     * @param Warning $e
     * @param float $time
     * @return void
     */
    public function addWarning(Test $test, Warning $e, $time)
    {
        echo "\t[";
        echo Colorize::text($e->getMessage(), "red", null, true);
        echo "] ";
    }

    /**
     * @param Test $test
     * @param AssertionFailedError $e
     * @param float $time
     * @return void
     */
    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
        echo "\t[";
        echo Colorize::text($e->getMessage(), "white", "red", true);
        echo "] ";
    }

    /**
     * @param Test $test
     * @param \Exception $e
     * @param float $time
     * @return void
     */
    public function addIncompleteTest(Test $test, \Exception $e, $time)
    {
        // incomplete additional text
    }

    /**
     * @param Test $test
     * @param \Exception $e
     * @param float $time
     * @return void
     */
    public function addRiskyTest(Test $test, \Exception $e, $time)
    {
        echo "\t[";
        echo Colorize::text($e->getMessage(), 'yellow', null, true);
        echo "] ";
    }

    /**
     * @param Test $test
     * @param \Exception $e
     * @param float $time
     * @return void
     */
    public function addSkippedTest(Test $test, \Exception $e, $time)
    {
        // skipped additional text
        echo "\t[";
        echo Colorize::text($e->getMessage(), 'cyan', null, true);
        echo "] ";
    }

    /**
     * @param Test $test
     * @return void
     */
    public function startTest(Test $test)
    {
        $name = sprintf('%-40.40s', $test->getName());

        echo "\n\t-> " . Colorize::text($name, 'green', null, true);
    }

    /**
     * @param Test $test
     * @param float $time
     * @return void
     */
    public function endTest(Test $test, $time)
    {
        $time = sprintf('%0.3f sec', $time);

        echo Colorize::text("\t[" . $test->count() . ']', 'white', null, true);
        echo Colorize::text("\t" . $time, 'green', null, true);
    }

    /**
     * @param TestSuite $suite
     * @return void
     */
    public function startTestSuite(TestSuite $suite)
    {
        $this->timeSuite = microtime(1);
        echo "\n\n";
        echo Colorize::text($suite->getName(), 'white', null, true);
    }

    /**
     * @param TestSuite $suite
     * @return void
     */
    public function endTestSuite(TestSuite $suite)
    {
        $time = sprintf('%0.3f sec', microtime(1) - $this->timeSuite);
        echo "\n";
        echo Colorize::text("Suite Time: ".$time, 'white', null, true);
        echo "\n";
    }
}
