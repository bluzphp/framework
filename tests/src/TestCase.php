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
use Bluz\Proxy\Request;
use Bluz\Request\RequestFactory;
use Zend\Diactoros\ServerRequest;

/**
 * Bluz TestCase
 *
 * @package  Bluz\Tests
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
     * Setup TestCase
     */
    protected function setUp()
    {
    }

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
     * Set new Request instance
     *
     * @param string $path Path part of URI http://host/module/controller/path
     * @param array  $query $_GET params
     * @param array  $params $_POST params
     * @param string $method HTTP method
     * @param array  $headers HTTP headers
     * @return \Psr\Http\Message\ServerRequestInterface|ServerRequest
     */
    protected function prepareRequest(
        $path = '',
        $query = [],
        $params = [],
        $method = Request::METHOD_GET,
        $headers = []
    ) {
        $uri = 'http://127.0.0.1/'. $path;

        $request = new ServerRequest([], [], $uri, $method, 'php://input', $headers);

        if (!empty($query)) {
            $request = $request->withQueryParams($query);
        }

        if (!empty($params)) {
            $request = $request->withParsedBody($params);
        }

        return $request;
    }

    /**
     * Set new Request instance
     *
     * @param string $path Path part of URI http://host/module/controller/path
     * @param array  $query $_GET params
     * @param array  $params $_POST params
     * @param string $method HTTP method
     * @param array  $headers HTTP headers
     * @return \Psr\Http\Message\ServerRequestInterface|ServerRequest
     */
    protected function setRequestParams(
        $path = '',
        $query = [],
        $params = [],
        $method = Request::METHOD_GET,
        $headers = []
    ) {
        $request = $this->prepareRequest($path, $query, $params, $method, $headers);

        Request::setInstance($request);

        return $request;
    }

    /**
     * Reset layout and Request
     */
    protected static function resetApp()
    {
        if (self::$app) {
            self::$app->useLayout(true);
            self::$app->resetRouter();
        }

        Proxy\Auth::clearIdentity();
        Proxy\Messages::popAll();
        Proxy\Request::setInstance(RequestFactory::fromGlobals());
        Proxy\Response::setInstance(new Bluz\Response\Response());
    }

    /**
     * Reset super-globals variables
     */
    protected static function resetGlobals()
    {
        $_GET = $_POST = array();
        unset($_SERVER['HTTP_X_REQUESTED_WITH'], $_SERVER['HTTP_ACCEPT']);
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
