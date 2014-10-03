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
use Bluz\EventManager\EventManager;

/**
 * EventManagerTest
 *
 * @package  Bluz\Tests
 *
 * @author   Anton Shevchuk
 * @created  14.05.2014 11:57
 */
class EventManagerTest extends Bluz\Tests\TestCase
{
    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->eventManager = new EventManager();
    }

    /**
     * Test one event
     */
    public function testTriggerSimpleEvent()
    {
        $counter = 0;
        $this->eventManager->attach('test', function () use (&$counter) {
            $counter++;
        });
        $this->eventManager->trigger('test');
        $this->assertEquals(1, $counter);
    }

    /**
     * Test three events
     */
    public function testTriggerThreeEvents()
    {
        $counter = 0;
        $this->eventManager->attach('test', function () use (&$counter) {
            $counter++;
        });
        $this->eventManager->attach('test', function () use (&$counter) {
            $counter++;
        });
        $this->eventManager->attach('test', function () use (&$counter) {
            $counter++;
        });
        $this->eventManager->trigger('test');
        $this->assertEquals(3, $counter);
    }

    /**
     * Test events with priority
     */
    public function testTriggerTwoEventsWithPriority()
    {
        $counter = 0;
        $this->eventManager->attach('test', function () use (&$counter) {
            $counter *= 2;
        }, 2);
        $this->eventManager->attach('test', function () use (&$counter) {
            $counter++;
        });
        $this->eventManager->trigger('test');
        $this->assertEquals(2, $counter);
    }

    /**
     * Test events with abort chain call
     */
    public function testTriggerTwoEventsWithAbort()
    {
        $counter = 0;
        $this->eventManager->attach('test', function () use (&$counter) {
            $counter++;
            return false;
        });
        $this->eventManager->attach('test', function () use (&$counter) {
            $counter *= 2;
        });
        $this->eventManager->trigger('test');
        $this->assertEquals(1, $counter);
    }

    /**
     * Test usage of namespaces
     */
    public function testTriggerWithNamespace()
    {
        $counter = 0;
        // namespace is first
        $this->eventManager->attach('some', function () use (&$counter) {
            $counter++;
        });
        // event is secondary
        $this->eventManager->attach('some:test', function () use (&$counter) {
            $counter *= 2;
        });
        $this->eventManager->trigger('some:test');
        $this->assertEquals(2, $counter);
    }

    /**
     * Test target usage
     */
    public function testTriggerWithTarget()
    {
        // first
        $this->eventManager->attach('test', function (/* @var Event */ $event) {
            return $event->getTarget() + 1;
        });
        // second
        $this->eventManager->attach('test', function (/* @var Event */ $event) {
            return $event->getTarget() + 1;
        });

        $counter = 0;

        $result = $this->eventManager->trigger('test', $counter);

        $this->assertEquals(2, $result);
    }

    /**
     * Test params
     */
    public function testTriggerWithParams()
    {
        $this->eventManager->attach('test', function (/* @var Event */ $event) {
            return $event->getTarget() + $event->getParam('plus');
        });

        $result = $this->eventManager->trigger('test', 10, ['plus'=>10]);

        $this->assertEquals(20, $result);
    }

    /**
     * Test wrong params
     * @expectedException Bluz\EventManager\EventException
     */
    public function testEventSetParamsException()
    {
        $this->eventManager->trigger('test', null, 'wrong type params');
    }
}
