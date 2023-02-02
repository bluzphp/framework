<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

namespace Bluz\Tests\Unit\Proxy;

use Bluz\Registry\Registry as Target;
use Bluz\Proxy\Registry as Proxy;
use Bluz\Tests\Unit\Unit;

/**
 * Proxy Test
 *
 * @package  Bluz\Tests\Proxy
 * @author   Anton Shevchuk
 */
class RegistryTest extends Unit
{
    public function testGetProxyInstance()
    {
        self::assertInstanceOf(Target::class, Proxy::getInstance());
    }

    public function testGetDataFromRegistry()
    {
        self::assertEquals('baz', Proxy::get('moo'));
    }
}
