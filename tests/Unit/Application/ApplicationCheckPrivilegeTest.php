<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit\Application;

use Bluz\Http\RequestMethod;
use Bluz\Http\StatusCode;
use Bluz\Proxy\Auth;
use Bluz\Proxy\Response;
use Bluz\Proxy\Router;
use Bluz\Tests\Fixtures\Models\UserAdmin;
use Bluz\Tests\Fixtures\Models\UserMember;
use Bluz\Tests\Unit\Unit;
use Psr\Cache\CacheException;

/**
 * @author   Anton Shevchuk
 */
class ApplicationCheckPrivilegeTest extends Unit
{
    /**
     * @dataProvider providerForPass
     *
     * @param string $path
     * @param array $privileges
     * @return void
     * @throws CacheException
     */
    public function testMethodWithValidData(string $path, array $privileges = []): void
    {
        // setup Request
        self::setRequestParams($path);

        $identity = new UserMember();
        $identity->setPrivileges($privileges);

        Auth::setIdentity($identity);

        // run Application
        self::getApp()->process();

        self::assertEquals('privilege', self::getApp()->getModule());
        self::assertEquals(StatusCode::OK, Response::getStatusCode());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param string $path
     * @param array $privileges
     * @return void
     * @throws CacheException
     */
    public function testMethodWithInvalidData(string $path, array $privileges = []): void
    {
        // setup Request
        self::setRequestParams($path);

        $identity = new UserMember();
        $identity->setPrivileges($privileges);

        Auth::setIdentity($identity);

        // run Application
        self::getApp()->process();

        self::assertEquals(Router::getErrorModule(), self::getApp()->getModule());
        self::assertEquals(Router::getErrorController(), self::getApp()->getController());
        self::assertEquals(StatusCode::FORBIDDEN, Response::getStatusCode());
    }

    /**
     * @return array
     */
    public static function providerForPass(): array
    {
        return [
            ['privilege/index', []],
            ['privilege/some', ['privilege:some']],
            ['privilege/many', ['privilege:one']],
            ['privilege/many', ['privilege:one', 'privilege:two']],
            ['privilege/many', ['privilege:one', 'privilege:two', 'privilege:three']],
        ];
    }
    /**
     * @return array
     */
    public static function providerForFail(): array
    {
        return [
            ['privilege/some', []],
            ['privilege/some', ['privilege:another']],
            ['privilege/some', ['privilege:one', 'privilege:two', 'privilege:three']],
            ['privilege/many', ['privilege:some']],
        ];
    }
}
