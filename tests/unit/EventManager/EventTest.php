<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests;

use Bluz;
use Bluz\EventManager\Event;
use Bluz\EventManager\EventException;

/**
 * EventTest
 *
 * @package  Bluz\Tests
 *
 * @author   Anton Shevchuk
 * @created  14.05.2014 11:57
 */
class EventTest extends Bluz\Tests\FrameworkTestCase
{
    /**
     * Complex test of event getters
     * @throws EventException
     */
    public function testEventMethods()
    {
        $event = new Event('test', 'target', ['foo' => 'bar']);

        self::assertEquals('test', $event->getName());
        self::assertEquals('target', $event->getTarget());
        self::assertEquals(['foo' => 'bar'], $event->getParams());
        self::assertEquals('bar', $event->getParam('foo'));
        self::assertNull($event->getParam('baz'));
        self::assertEquals('qux', $event->getParam('baz', 'qux'));

        $event->setParam('baz', 'qux');
        self::assertEquals('qux', $event->getParam('baz'));
    }

    /**
     * Test trigger with wong params
     */
    public function testEventSetParamsException()
    {
        $this->expectException(\TypeError::class);
        new Event('test', null, 'wrong type');
    }
}
