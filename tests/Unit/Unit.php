<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit;

use Bluz\Application\Application;
use Bluz\Application\ApplicationException;
use Bluz\Http;
use Bluz\Proxy;
use Codeception\Test\Unit as TestUnit;
use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Bluz TestCase
 *
 * @package  Bluz\Tests
 *
 * @author   Anton Shevchuk
 * @created  04.08.11 20:01
 */
class Unit extends TestUnit
{
    /**
     * Application entity
     *
     * @var Application|null
     */
    protected static ?Application $app = null;

    /**
     * Setup TestCase
     */
    protected function setUp(): void
    {
        self::getApp();
    }

    /**
     * Tear Down
     */
    protected function tearDown(): void
    {
        self::resetApp();
    }

    /**
     * Get Application instance
     *
     * @return Application
     * @throws ApplicationException
     */
    protected static function getApp(): Application
    {
        if (!self::$app) {
            $env = getenv('BLUZ_ENV') ?: 'testing';
            self::$app = Bootstrap::getInstance();
            self::$app->setEnvironment($env);
            self::$app->init();
        }    // Environment
        return self::$app;
    }

    /**
     * Reset layout and Request
     */
    protected static function resetApp()
    {
        if (self::$app) {
            self::$app::resetInstance();
            self::$app = null;
        }

        Proxy\Acl::resetInstance();
        Proxy\Auth::resetInstance();
        Proxy\Cache::resetInstance();
        Proxy\Config::resetInstance();
        Proxy\Db::resetInstance();
        Proxy\EventManager::resetInstance();
        Proxy\HttpCacheControl::resetInstance();
        Proxy\Layout::resetInstance();
        Proxy\Logger::resetInstance();
        Proxy\Mailer::resetInstance();
        Proxy\Messages::resetInstance();
        Proxy\Registry::resetInstance();
        Proxy\Request::resetInstance();
        Proxy\Request::resetAccept();
        Proxy\Response::resetInstance();
        Proxy\Router::resetInstance();
        Proxy\Session::resetInstance();
        Proxy\Translator::resetInstance();
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
     * @param string|null $message
     */
    protected static function assertEqualsArray(array $expected, array $actual, string $message = null)
    {
        self::assertSame(
            array_diff($expected, $actual),
            array_diff($actual, $expected),
            $message ?: 'Failed asserting that two arrays is equals.'
        );
    }

    /**
     * Assert Array Size
     *
     * @param array|\ArrayObject $array
     * @param int $size
     * @param string $message
     */
    protected static function assertArrayHasSize($array, $size, $message = null)
    {
        self::assertCount(
            $size,
            $array,
            $message ?: 'Failed asserting that array has size ' . $size . ' matches expected ' . count($array) . '.'
        );
    }

    /**
     * Assert Array Key has Size
     *
     * @param array|\ArrayObject $array
     * @param string $key
     * @param int $size
     * @param string|null $message
     */
    protected static function assertArrayHasKeyAndSize($array, string $key, int $size, string $message = null)
    {
        if (!$message) {
            $message = 'Failed asserting that array has key ' . $key . ' with size ' . $size
                . ' matches expected ' . count($array) . '.';
        }

        self::assertArrayHasKey($key, $array, $message);
        self::assertCount($size, $array[$key], $message);
    }

    /**
     * Set new Request instance
     *
     * @param string $path Path part of URI http://host/module/controller/path
     * @param array $query $_GET params
     * @param array $params $_POST params
     * @param Http\RequestMethod $method HTTP method
     * @param array $headers HTTP headers
     * @param array $cookies
     *
     * @return ServerRequestInterface|ServerRequest
     */
    protected static function prepareRequest(
        string $path = '',
        array $query = [],
        array $params = [],
        Http\RequestMethod $method = Http\RequestMethod::GET,
        array $headers = [],
        array $cookies = []
    ) {
        $uri = 'http://127.0.0.1/' . $path;

        return new ServerRequest([], [], $uri, $method->value, 'php://input', $headers, $cookies, $query, $params);
    }

    /**
     * Set new Request instance
     *
     * @param string $path Path part of URI http://host/module/controller/path
     * @param array $query $_GET params
     * @param array $params $_POST params
     * @param Http\RequestMethod $method HTTP method
     * @param array $headers HTTP headers
     * @param array $cookies
     *
     * @return ServerRequestInterface|ServerRequest
     */
    protected static function setRequestParams(
        string $path = '',
        array $query = [],
        array $params = [],
        Http\RequestMethod $method = Http\RequestMethod::GET,
        array $headers = [],
        array $cookies = []
    ) {
        $request = self::prepareRequest($path, $query, $params, $method, $headers, $cookies);

        Proxy\Request::setInstance($request);

        return $request;
    }
}
