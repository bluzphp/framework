<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
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
        foreach ($listeners as $list) {
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
