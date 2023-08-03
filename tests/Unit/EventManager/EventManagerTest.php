<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit\EventManager;

use Bluz;
use Bluz\EventManager\Event;
use Bluz\EventManager\EventManager;
use Bluz\Tests\Unit\Unit;

/**
 * EventManagerTest
 *
 * @package  Bluz\Tests
 *
 * @author   Anton Shevchuk
 * @created  14.05.2014 11:57
 */
class EventManagerTest extends Unit
{
    /**
     * @var EventManager
     */
    protected EventManager $eventManager;

    /**
     * setUp
     */
    public function setUp(): void
    {
        $this->eventManager = new EventManager();
    }

    /**
     * Test one event
     */
    public function testTriggerSimpleEvent()
    {
        $counter = 0;
        $this->eventManager->attach(
            'test',
            function () use (&$counter) {
                $counter++;
            }
        );
        $this->eventManager->trigger('test');
        self::assertEquals(1, $counter);
    }

    /**
     * Test three events
     */
    public function testTriggerThreeEvents()
    {
        $counter = 0;
        $this->eventManager->attach(
            'test',
            function () use (&$counter) {
                $counter++;
            }
        );
        $this->eventManager->attach(
            'test',
            function () use (&$counter) {
                $counter++;
            }
        );
        $this->eventManager->attach(
            'test',
            function () use (&$counter) {
                $counter++;
            }
        );
        $this->eventManager->trigger('test');
        self::assertEquals(3, $counter);
    }

    /**
     * Test events with priority
     */
    public function testTriggerTwoEventsWithPriority()
    {
        $counter = 0;
        $this->eventManager->attach(
            'test',
            function () use (&$counter) {
                $counter *= 2;
            },
            2
        );
        $this->eventManager->attach(
            'test',
            function () use (&$counter) {
                $counter++;
            }
        );
        $this->eventManager->trigger('test');
        self::assertEquals(2, $counter);
    }

    /**
     * Test events with abort chain call
     */
    public function testTriggerTwoEventsWithAbort()
    {
        $counter = 0;
        $this->eventManager->attach(
            'test',
            function () use (&$counter) {
                $counter++;
                return false;
            }
        );
        $this->eventManager->attach(
            'test',
            function () use (&$counter) {
                $counter *= 2;
            }
        );
        $this->eventManager->trigger('test');
        self::assertEquals(1, $counter);
    }

    /**
     * Test usage of namespaces
     */
    public function testTriggerWithNamespace()
    {
        $counter = 0;
        // namespace is first
        $this->eventManager->attach(
            'some',
            function () use (&$counter) {
                $counter++;
            }
        );
        // event is secondary
        $this->eventManager->attach(
            'some:test',
            function () use (&$counter) {
                $counter *= 2;
            }
        );
        $this->eventManager->trigger('some:test');
        self::assertEquals(2, $counter);
    }

    /**
     * Test target usage
     */
    public function testTriggerWithTarget()
    {
        // first
        $this->eventManager->attach(
            'test',
            function ($event) {
                /* @var Event */
                return $event->getTarget() + 1;
            }
        );
        // second
        $this->eventManager->attach(
            'test',
            function ($event) {
                /* @var Event */
                return $event->getTarget() + 1;
            }
        );

        $counter = 0;

        $result = $this->eventManager->trigger('test', $counter);

        self::assertEquals(2, $result);
    }

    /**
     * Test params
     */
    public function testTriggerWithParams()
    {
        $this->eventManager->attach(
            'test',
            function ($event) {
                /* @var Event */
                return $event->getTarget() + $event->getParam('plus');
            }
        );

        $result = $this->eventManager->trigger('test', 10, ['plus' => 10]);

        self::assertEquals(20, $result);
    }

    /**
     * Test wrong params
     */
    public function testEventSetParamsException()
    {
        $this->expectException(\TypeError::class);
        $this->eventManager->trigger('test', null, 'wrong type params');
    }
}