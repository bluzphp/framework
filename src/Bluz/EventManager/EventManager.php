<?php
/**
 * Copyright (c) 2012 by Bluz PHP Team
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @namespace
 */
namespace Bluz\EventManager;

/**
 * Event manager
 *
 * @category Bluz
 * @package  EventManager
 */
class EventManager
{
    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * attach
     *
     * @param string $eventName
     * @param \closure $callback
     * @param int $priority
     * @return EventManager
     */
    public function attach($eventName, $callback, $priority = 1)
    {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = array();
        }
        if (!isset($this->listeners[$eventName][$priority])) {
            $this->listeners[$eventName][$priority] = array();
        }
        $this->listeners[$eventName][$priority][] = $callback;
        return $this;
    }

    /**
     * trigger
     *
     * @param      $event
     * @param null $target
     * @param null $params
     * @return EventManager
     */
    public function trigger($event, $target = null, $params = null)
    {
        if (!$event instanceof Event) {
            $event = new Event($event, $target, $params);
        }

        if (strstr($event->getName(), ':')) {
            $namespace = substr($event->getName(), 0, strpos($event->getName(), ':'));

            if (isset($this->listeners[$namespace])) {
                $this->fire($this->listeners[$namespace], $event);
            }
        }

        if (isset($this->listeners[$event->getName()])) {
            $this->fire($this->listeners[$event->getName()], $event);
        }

        return $event->getTarget();
    }

    /**
     * fire
     *
     * @param $listeners
     * @param Event $event
     * @return EventManager
     */
    protected function fire($listeners, $event)
    {
        ksort($listeners);
        foreach($listeners as $list) {
            foreach ($list as $listener) {
                $result = call_user_func($listener, $event);
                if (null === $result) {
                    // continue;
                } elseif (false === $result) {
                    break 2;
                } else {
                    $event->setTarget($result);
                }
            }
        }
        return $this;
    }
}
