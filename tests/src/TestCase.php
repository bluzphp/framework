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
use Bluz\Router\Router;
use Codeception\Test\Unit;
use Zend\Diactoros\ServerRequest;

/**
 * Bluz TestCase
 *
 * @package  Bluz\Tests
 *
 * @author   Anton Shevchuk
 * @created  04.08.11 20:01
 */
class TestCase extends Unit
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
     * @param array $cookies
     * @return \Psr\Http\Message\ServerRequestInterface|ServerRequest
     */
    protected static function prepareRequest(
        $path = '',
        $query = [],
        $params = [],
        $method = Http\RequestMethod::GET,
        $headers = [],
        $cookies = []
    ) {
        $uri = 'http://127.0.0.1/'. $path;

        return new ServerRequest([], [], $uri, $method, 'php://input', $headers, $cookies, $query, $params);
    }

    /**
     * Set new Request instance
     *
     * @param string $path Path part of URI http://host/module/controller/path
     * @param array  $query $_GET params
     * @param array  $params $_POST params
     * @param string $method HTTP method
     * @param array  $headers HTTP headers
     * @param array $cookies
     * @return \Psr\Http\Message\ServerRequestInterface|ServerRequest
     */
    protected static function setRequestParams(
        $path = '',
        $query = [],
        $params = [],
        $method = Http\RequestMethod::GET,
        $headers = [],
        $cookies = []
    ) {

        $request = self::prepareRequest($path, $query, $params, $method, $headers, $cookies);

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
     * resetRouter
     *
     * @return void
     */
    protected static function resetRouter()
    {
        $router = new Router();
        $router->setBaseUrl('/');
        Proxy\Router::setInstance($router);
    }

    /**
     * Reset super-globals variables
     */
    protected static function resetGlobals()
    {
        $_GET = $_POST = [];
        unset($_SERVER['HTTP_X_REQUESTED_WITH'], $_SERVER['HTTP_ACCEPT']);
    }

    /**
     * Assert one-level Arrays is Equals
     *
     * @param array $expected
     * @param array $actual
     * @param string $message
     */
    protected static function assertEqualsArray($expected, $actual, $message = null)
    {
        self::assertSame(
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
    protected static function assertArrayHasSize($array, $size, $message = null)
    {
        self::assertCount(
            $size,
            $array,
            $message ?: 'Failed asserting that array has size '.$size.' matches expected '.count($array). '.'
        );
    }

    /**
     * Assert Array Key has Size
     * @param array|\ArrayObject $array
     * @param string $key
     * @param integer $size
     * @param string $message
     */
    protected static function assertArrayHasKeyAndSize($array, $key, $size, $message = null)
    {
        if (!$message) {
            $message = 'Failed asserting that array has key '.$key.' with size '.$size
                . ' matches expected '.count($array). '.';
        }

        self::assertArrayHasKey($key, $array, $message);
        self::assertCount($size, $array[$key], $message);
    }
}
