<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Proxy;

use Bluz\Router\Router as Target;
use Bluz\Proxy\Router as Proxy;
use Bluz\Tests\FrameworkTestCase;

/**
 * Proxy Test
 *
 * @package  Bluz\Tests\Proxy
 * @author   Anton Shevchuk
 */
class RouterTest extends FrameworkTestCase
{
    protected function setUp()
    {
        Proxy::resetInstance();
    }

    /**
     * @expectedException \Bluz\Common\Exception\ComponentException
     */
    public function testLazyInitialInstanceShouldThrowError()
    {
        self::assertInstanceOf(Target::class, Proxy::getInstance());
    }
}
