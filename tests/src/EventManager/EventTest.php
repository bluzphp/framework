<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests;

use Bluz;
use Bluz\EventManager\Event;

/**
 * EventTest
 *
 * @package  Bluz\Tests
 *
 * @author   Anton Shevchuk
 * @created  14.05.2014 11:57
 */
class EventTest extends Bluz\Tests\TestCase
{
    /**
     * Complex test of event getters
     */
    public function testEventMethods()
    {
        $event = new Event('test', 'target', ['foo'=>'bar']);

        $this->assertEquals('test', $event->getName());
        $this->assertEquals('target', $event->getTarget());
        $this->assertEquals(['foo'=>'bar'], $event->getParams());
        $this->assertEquals('bar', $event->getParam('foo'));
        $this->assertNull($event->getParam('baz'));
        $this->assertEquals('qux', $event->getParam('baz', 'qux'));

        $event->setParam('baz', 'qux');
        $this->assertEquals('qux', $event->getParam('baz'));
    }

    /**
     * Complex test of event getters with object
     */
    public function testEventMethodsWithObject()
    {
        $params = new \stdClass();
        $params->foo = 'bar';

        $event = new Event('test', 'target', $params);

        $this->assertEquals($params, $event->getParams());
        $this->assertEquals('bar', $event->getParam('foo'));
        $this->assertNull($event->getParam('baz'));
        $this->assertEquals('qux', $event->getParam('baz', 'qux'));

        $event->setParam('baz', 'qux');
        $this->assertEquals('qux', $event->getParam('baz'));
    }

    /**
     * Test trigger with wong params
     * @expectedException \Bluz\EventManager\EventException
     */
    public function testEventSetParamsException()
    {
        new Event('test', null, 'wrong type');
    }
}
