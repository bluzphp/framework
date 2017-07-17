<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Proxy;

use Bluz\Common\Nil as Target;
use Bluz\Proxy\HttpCacheControl as Proxy;
use Bluz\Tests\FrameworkTestCase;

/**
 * Proxy Test
 *
 * @package  Bluz\Tests\Proxy
 * @author   Anton Shevchuk
 */
class HttpCacheControlTest extends FrameworkTestCase
{
    /**
     * Test instance
     */
    public function testProxyInstanceReturnNilForCLI()
    {
        self::assertInstanceOf(Target::class, Proxy::getInstance());
    }
}
