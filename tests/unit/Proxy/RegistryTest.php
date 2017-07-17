<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Proxy;

use Bluz\Registry\Registry as Target;
use Bluz\Proxy\Registry as Proxy;
use Bluz\Tests\FrameworkTestCase;

/**
 * Proxy Test
 *
 * @package  Bluz\Tests\Proxy
 * @author   Anton Shevchuk
 */
class RegistryTest extends FrameworkTestCase
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
     * Test instance
     */
    public function testProxyInstance()
    {
        self::assertInstanceOf(Target::class, Proxy::getInstance());
        self::assertEquals('baz', Proxy::get('moo'));
    }
}
