<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

namespace Bluz\Tests\Unit\Proxy;

use Bluz\Common\Exception\ComponentException;
use Bluz\Router\Router as Target;
use Bluz\Proxy\Router as Proxy;
use Bluz\Tests\Unit\Unit;

/**
 * Proxy Test
 *
 * @package  Bluz\Tests\Proxy
 * @author   Anton Shevchuk
 */
class RouterTest extends Unit
{
    public function testGetAlreadyInitedProxyInstance()
    {
        self::assertInstanceOf(Target::class, Proxy::getInstance());
    }

    public function testLazyInitialInstanceShouldThrowError()
    {
        $this->expectException(ComponentException::class);
        Proxy::resetInstance();
        Proxy::getInstance();
    }
}
