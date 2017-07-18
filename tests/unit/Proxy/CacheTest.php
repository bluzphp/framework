<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Proxy;

use Bluz\Proxy\Cache as Proxy;
use Bluz\Tests\FrameworkTestCase;

/**
 * CacheTest
 *
 * @package  Bluz\Tests\Proxy
 * @author   Anton Shevchuk
 */
class CacheTest extends FrameworkTestCase
{
    protected function setUp()
    {
        Proxy::resetInstance();
    }

    public function testGetProxyInstanceReturnFalse()
    {
        self::assertFalse(Proxy::getInstance());
    }
}
