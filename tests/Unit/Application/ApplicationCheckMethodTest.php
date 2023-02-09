<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit\Application;

use Bluz\Http\RequestMethod;
use Bluz\Http\StatusCode;
use Bluz\Proxy\Response;
use Bluz\Proxy\Router;
use Bluz\Tests\Unit\Unit;
use Psr\Cache\CacheException;

/**
 * @author   Anton Shevchuk
 */
class ApplicationCheckMethodTest extends Unit
{
    /**
     * @dataProvider providerForPass
     *
     * @param string $path
     * @param RequestMethod $method
     * @return void
     * @throws CacheException
     */
    public function testMethodWithValidData(string $path, RequestMethod $method): void
    {
        // setup Request
        self::setRequestParams(
            path: $path,
            query: [],
            params: [],
            method: $method
        );

        // run Application
        self::getApp()->process();

        self::assertEquals('methods', self::getApp()->getModule());
        self::assertEquals(StatusCode::OK, Response::getStatusCode());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param string $path
     * @param RequestMethod $method
     * @return void
     * @throws CacheException
     */
    public function testMethodWithInvalidData(string $path, RequestMethod $method): void
    {
        // setup Request
        // setup Request
        self::setRequestParams(
            path: $path,
            query: [],
            params: [],
            method: $method
        );

        // run Application
        self::getApp()->process();

        self::assertEquals(Router::getErrorModule(), self::getApp()->getModule());
        self::assertEquals(Router::getErrorController(), self::getApp()->getController());
        self::assertEquals(StatusCode::METHOD_NOT_ALLOWED, Response::getStatusCode());
    }

    /**
     * @return array
     */
    public function providerForPass(): array
    {
        return [
            ['methods/get', RequestMethod::GET],
            ['methods/post', RequestMethod::POST],
            ['methods/get-and-post', RequestMethod::GET],
            ['methods/get-and-post', RequestMethod::POST],
        ];
    }
    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            ['methods/get', RequestMethod::POST],
            ['methods/post', RequestMethod::GET],
            ['methods/get-and-post', RequestMethod::PUT],
            ['methods/get-and-post', RequestMethod::PATCH],
        ];
    }
}
