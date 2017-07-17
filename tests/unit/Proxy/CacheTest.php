<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Proxy;

use Bluz\Proxy\Cache;
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
     * Test disabled Cache
     */
    public function testDisabledTest()
    {
        self::assertFalse(Cache::getInstance());
    }
}
