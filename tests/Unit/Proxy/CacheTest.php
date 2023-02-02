<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

namespace Bluz\Tests\Unit\Proxy;

use Bluz\Proxy\Cache as Proxy;
use Bluz\Tests\Unit\Unit;

/**
 * CacheTest
 *
 * @package  Bluz\Tests\Proxy
 * @author   Anton Shevchuk
 */
class CacheTest extends Unit
{
    public function testGetProxyInstanceReturnFalse()
    {
        self::assertFalse(Proxy::getInstance());
    }
}
