<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit\Common;

use Bluz\Tests\Unit\Unit;
use Bluz\Tests\Fixtures\Common\ConcreteSingleton;

/**
 * SingletonTest
 *
 * @package  Bluz\Tests\Common
 *
 * @author   Anton Shevchuk
 * @created  12.08.2014 13:24
 */
class SingletonTest extends Unit
{
    /**
     * Test GetInstance
     */
    public function testGetInstance()
    {
        $result = ConcreteSingleton::getInstance();
        $result->foo = 'bar';

        self::assertInstanceOf(ConcreteSingleton::class, $result);
        self::assertEquals(ConcreteSingleton::getInstance(), $result);
        self::assertEquals('bar', ConcreteSingleton::getInstance()->foo);
    }

    /**
     * Test True Singleton
     */
    public function testGetTheSameInstance()
    {
        self::assertSame(ConcreteSingleton::getInstance(), ConcreteSingleton::getInstance());
    }

    /**
     * Test Clone
     */
    public function testPrivateMethods()
    {
        $result = ConcreteSingleton::getInstance();

        $reflection = new \ReflectionObject($result);

        self::assertTrue($reflection->getMethod('__construct')->isPrivate());
        self::assertTrue($reflection->getMethod('__clone')->isPrivate());
    }
}
