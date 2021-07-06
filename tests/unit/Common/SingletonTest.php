<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Common;

use Bluz\Tests\FrameworkTestCase;
use Bluz\Tests\Fixtures\Common\ConcreteSingleton;

/**
 * SingletonTest
 *
 * @package  Bluz\Tests\Common
 *
 * @author   Anton Shevchuk
 * @created  12.08.2014 13:24
 */
class SingletonTest extends FrameworkTestCase
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
     * Test Clone
     */
    public function testPrivateMethods()
    {
        $result = ConcreteSingleton::getInstance();

        $reflection = new \ReflectionObject($result);

        self::assertTrue($reflection->getMethod('__construct')->isPrivate());
        self::assertTrue($reflection->getMethod('__clone')->isPrivate());
    }

    /**
     * Test Construct Throw Error
     */
    public function testConstructThrowError()
    {
        $this->expectException(\Error::class);
        new ConcreteSingleton();
    }

    /**
     * Test Clone Throw Error
     */
    public function testCloneThrowError()
    {
        $this->expectException(\Error::class);
        clone ConcreteSingleton::getInstance();
    }
}
