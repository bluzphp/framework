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

/**
 * @category Bluz
 * @package  Tests
 */
class TestListener implements \PHPUnit_Framework_TestListener
{
    /**
     * time of suite
     *
     * @var integer
     */
    protected $timeSuite = 0;
    
    /**
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception $e
     * @param $time
     * @return void
     */
    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        echo "\t[";
        echo Colorize::text($e->getMessage(), "red", null, true);
        echo "] ";
    }

    /**
     * @param \PHPUnit_Framework_Test $test
     * @param \PHPUnit_Framework_AssertionFailedError $e
     * @param $time
     * @return void
     */
    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        echo "\t[";
        echo Colorize::text($e->getMessage(), "white", "red", true);
        echo "] ";
    }

    /**
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception $e
     * @param $time
     * @return void
     */
    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        // incomplete additional text
    }

    /**
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception $e
     * @param $time
     * @return void
     */
    public function addRiskyTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        echo "\t[";
        echo Colorize::text($e->getMessage(), 'yellow', null, true);
        echo "] ";
    }

    /**
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception $e
     * @param $time
     * @return void
     */
    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        // skipped additional text
        echo "\t[";
        echo Colorize::text($e->getMessage(), 'cyan', null, true);
        echo "] ";
    }

    /**
     * @param \PHPUnit_Framework_Test $test
     * @return void
     */
    public function startTest(\PHPUnit_Framework_Test $test)
    {
        $name = sprintf('%-40.40s', $test->getName());

        echo "\n\t-> " . Colorize::text($name, 'green', null, true);
    }

    /**
     * @param \PHPUnit_Framework_Test $test
     * @param $time
     * @return void
     */
    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        $time = sprintf('%0.3f sec', $time);

        echo Colorize::text("\t[" . $test->getCount() . ']', 'white', null, true);
        echo Colorize::text("\t" . $time, 'green', null, true);
    }

    /**
     * @param \PHPUnit_Framework_TestSuite $suite
     * @return void
     */
    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $this->timeSuite = microtime(1);
        echo "\n\n";
        echo Colorize::text($suite->getName(), 'white', null, true);
    }

    /**
     * @param \PHPUnit_Framework_TestSuite $suite
     * @return void
     */
    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $time = sprintf('%0.3f sec', microtime(1) - $this->timeSuite);
        echo "\n";
        echo Colorize::text("Suite Time: ".$time, 'white', null, true);
        echo "\n";
    }
}
