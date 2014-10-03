<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests;

use Bluz;
use Bluz\Http;
use Bluz\Proxy;

/**
 * ControllerTestCase
 *
 * @category Bluz
 * @package  Tests
 *
 * @author   Anton Shevchuk
 * @created  04.08.11 20:01
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Application entity
     *
     * @var \Application\Tests\BootstrapTest
     */
    static protected $app;

    /**
     * Tear Down
     */
    protected function tearDown()
    {
        self::resetApp();
    }

    /**
     * Get Application instance
     *
     * @return BootstrapTest
     */
    protected static function getApp()
    {
        if (!self::$app) {
            $env = getenv('BLUZ_ENV') ?: 'testing';

            self::$app = BootstrapTest::getInstance();
            self::$app->init($env);
        }
        return self::$app;
    }

    /**
     * Reset layout and Request
     */
    protected static function resetApp()
    {
        if (self::$app) {
            self::$app->useJson(false);
            self::$app->useLayout(true);
        }
        Proxy\Auth::clearIdentity();
        Proxy\Messages::popAll();
        Proxy\Request::setInstance(new Http\Request());
        Proxy\Response::setInstance(new Http\Response());
    }

    /**
     * Reset super-globals variables
     */
    protected static function resetGlobals()
    {
        $_GET = $_POST = array();
    }

    /**
     * Assert one-level Arrays is Equals
     *
     * @param array $expected
     * @param array $actual
     * @param string $message
     */
    protected function assertEqualsArray($expected, $actual, $message = null)
    {
        $this->assertSame(
            array_diff($expected, $actual),
            array_diff($actual, $expected),
            $message ?: 'Failed asserting that two arrays is equals.'
        );
    }

    /**
     * Assert Array Size
     * @param array|\ArrayObject $array
     * @param integer $size
     * @param string $message
     */
    protected function assertArrayHasSize($array, $size, $message = null)
    {
        $this->assertEquals(
            $size,
            sizeof($array),
            $message ?: 'Failed asserting that array has size '.$size.' matches expected '.sizeof($array). '.'
        );
    }

    /**
     * Assert Array Key has Size
     * @param array|\ArrayObject $array
     * @param string $key
     * @param integer $size
     * @param string $message
     */
    protected function assertArrayHasKeyAndSize($array, $key, $size, $message = null)
    {
        if (!$message) {
            $message = 'Failed asserting that array has key '.$key.' with size '.$size
                . ' matches expected '.sizeof($array). '.';
        }

        $this->assertArrayHasKey($key, $array, $message);
        $this->assertEquals($size, sizeof($array[$key]), $message);
    }
}
