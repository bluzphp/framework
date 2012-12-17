<?php
/**
 * Copyright (c) 2012 by Bluz PHP Team
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @namespace
 */
namespace Bluz\Tests;

/**
 * @category Bluz
 * @package  Tests
 */
class TestListener implements \PHPUnit_Framework_TestListener
{
    /**
     * time of test
     *
     * @var integer
     */
    protected $timeTest = 0;
    
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
        echo $this->colorize("error", "red");
        echo "]-";
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
        echo $this->colorize("failed", "red");
        echo "]-";
    }

    /**
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception $e
     * @param $time
     * @return void
     */
    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        echo "\t\t[";
        echo $this->colorize("incomplete");
        echo "]-";
    }

    /**
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception $e
     * @param $time
     * @return void
     */
    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        echo "\t[";
        echo $this->colorize("skipped");
        echo "]-";
    }

    /**
     * @param \PHPUnit_Framework_Test $test
     * @return void
     */
    public function startTest(\PHPUnit_Framework_Test $test)
    {
        $this->timeTest = microtime(1);
        $method = $this->colorize($test->getName(), 'green');

        echo "\n\t-> " . $method;
    }

    /**
     * @param \PHPUnit_Framework_Test $test
     * @param $time
     * @return void
     */
    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        $time = sprintf('%0.3f sec', microtime(1) - $this->timeTest);
        
        echo "\t\t" . $test->getCount() . '(Assertions)';
        echo $this->colorize("\t" . $time, 'green');
    }

    /**
     * @param \PHPUnit_Framework_TestSuite $suite
     * @return void
     */
    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $this->timeSuite = microtime(1);
        echo "\n\n".$this->colorize($suite->getName(), 'blue');
    }

    /**
     * @param \PHPUnit_Framework_TestSuite $suite
     * @return void
     */
    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $time = sprintf('%0.3f sec', microtime(1) - $this->timeSuite);

        echo $this->colorize("\nTime: ".$time, 'green');
    }

    /**
     * @param $text
     * @param string $color
     * @return string
     */
    private function colorize($text, $color = 'yellow')
    {
        switch ($color) {
            case 'red':
                $color = "1;31";
                break;
            case 'green':
                $color = "1;32";
                break;
            case 'blue':
                $color = "1;34";
                break;
            case 'white':
                $color = "1;37";
                break;
            default:
                $color = "1;33";
                break;
        }
        return "\033[" . $color .'m'. $text . "\033[0m";
    }
}