<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

namespace Bluz\Tests\Proxy;

use Bluz\Auth\Auth as Target;
use Bluz\Proxy\Auth as Proxy;
use Bluz\Tests\FrameworkTestCase;

/**
 * Proxy Test
 *
 * @package  Bluz\Tests\Proxy
 * @author   Anton Shevchuk
 */
class AuthTest extends FrameworkTestCase
{
    public function testGetProxyInstance()
    {
        self::assertInstanceOf(Target::class, Proxy::getInstance());
    }
}
