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
    /**
     * setUp
     *
     * @return void
     */
    protected function setUp()
    {
        Proxy::resetInstance();
    }

    /**
     * Test disabled Cache
     */
    public function testDisabledTest()
    {
        self::assertFalse(Proxy::getInstance());
    }
}
