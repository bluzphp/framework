<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
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
    public function testGetProxyInstanceReturnNilForCLI()
    {
        self::assertInstanceOf(Target::class, Proxy::getInstance());
    }
}
