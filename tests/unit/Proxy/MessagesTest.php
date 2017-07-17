<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Proxy;

use Bluz\Messages\Messages as Target;
use Bluz\Proxy\Messages as Proxy;
use Bluz\Tests\FrameworkTestCase;

/**
 * Proxy Test
 *
 * @package  Bluz\Tests\Proxy
 * @author   Anton Shevchuk
 */
class MessagesTest extends FrameworkTestCase
{
    protected function setUp()
    {
        Proxy::resetInstance();
    }

    public function testGetProxyInstance()
    {
        self::assertInstanceOf(Target::class, Proxy::getInstance());
    }
}
