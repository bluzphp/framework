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
use Bluz\Response\ResponseType;
use Bluz\Tests\Unit\Unit;
use Psr\Cache\CacheException;

/**
 * @author   Anton Shevchuk
 */
class ApplicationCheckAcceptTest extends Unit
{
    /**
     * @dataProvider providerForPass
     *
     * @param string $path
     * @param string $accept
     * @return void
     * @throws CacheException
     */
    public function testAcceptWithValidData(string $path, string $accept): void
    {
        // setup Request
        self::setRequestParams(
            path: $path,
            query: [],
            params: [],
            method: RequestMethod::GET,
            headers: [
                'Accept' => $accept
            ]
        );

        // run Application
        self::getApp()->process();

        self::assertEquals(StatusCode::OK, Response::getStatusCode());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param string $path
     * @param string $accept
     * @return void
     * @throws CacheException
     */
    public function testAcceptWithInvalidData(string $path, string $accept): void
    {
        // setup Request
        self::setRequestParams(
            path: $path,
            query: [],
            params: [],
            method: RequestMethod::GET,
            headers: [
                'Accept' => $accept
            ]
        );

        // run Application
        self::getApp()->process();

        self::assertEquals(Router::getErrorModule(), self::getApp()->getModule());
        self::assertEquals(Router::getErrorController(), self::getApp()->getController());
        self::assertEquals(StatusCode::NOT_ACCEPTABLE, Response::getStatusCode());
    }

    /**
     * @return array
     */
    public function providerForPass(): array
    {
        return [
            ['accept/any', ''],
            ['accept/any', '*/*'],
            ['accept/any', 'application/json'],
            ['accept/any', 'text/html,*/*;q=0.8'],
            ['accept/any', 'text/html,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9'],
            ['accept/any', 'text/html,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9'],
            ['accept/html', 'text/html,*/*;q=0.8'],
            ['accept/html', 'text/html,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9'],
            ['accept/index', ''],
            ['accept/index', '*/*'],
            ['accept/index', 'text/html,*/*;q=0.8'],
            ['accept/index', 'text/html,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9'],
            ['accept/json', 'application/json'],
            ['accept/json', '*/*,application/json;q=0.9'],
            ['accept/mixed', '*/*,application/json;q=0.9'],
            ['accept/mixed', 'text/html,*/*;q=0.8'],
        ];
    }
    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            ['accept/html', ''],
            ['accept/html', '*/*'],
            ['accept/html', '*/*,application/json;q=0.9'],
            ['accept/json', ''],
            ['accept/json', '*/*'],
            ['accept/mixed', ''],
            ['accept/mixed', '*/*'],
        ];
    }
}
