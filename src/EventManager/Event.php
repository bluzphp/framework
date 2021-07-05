<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\EventManager;

/**
 * Representation of an event
 *
 * @package  Bluz\EventManager
 */
class Event
{
    /**
     * @var string event name
     */
    protected $name;

    /**
     * @var string|object the event target
     */
    protected $target;

    /**
     * @var array|object the event parameters
     */
    protected $params = [];

    /**
     * Constructor
     *
     * Accept a target and its parameters.
     *
     * @param  string $name Event name
     * @param  string|object $target
     * @param  array|object  $params
     *
     * @throws EventException
     */
    public function __construct(string $name, $target = null, $params = null)
    {
        $this->setName($name);

        if (null !== $target) {
            $this->setTarget($target);
        }

        if (null !== $params) {
            $this->setParams($params);
        }
    }

    /**
     * Get event name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the event target
     *
     * This may be either an object, or the name of a static method.
     *
     * @return string|object
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Overwrites parameters
     *
     * @param  array|object $params
     *
     * @return void
     * @throws EventException
     */
    public function setParams($params): void
    {
        if (!is_array($params) && !is_object($params)) {
            throw new EventException(
                'Event parameters must be an array or object; received `' . gettype($params) . '`'
            );
        }

        $this->params = $params;
    }

    /**
     * Get all parameters
     *
     * @return array|object
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get an individual parameter
     *
     * If the parameter does not exist, the $default value will be returned.
     *
     * @param  string|int $name
     * @param  mixed      $default
     *
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        if (is_array($this->params)) {
            // Check in params that are arrays or implement array access
            return $this->params[$name] ?? $default;
        } elseif (is_object($this->params)) {
            // Check in normal objects
            return $this->params->{$name} ?? $default;
        } else {
            // Wrong type, return default value
            return $default;
        }
    }

    /**
     * Set the event name
     *
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Set the event target/context
     *
     * @param  null|string|object $target
     *
     * @return void
     */
    public function setTarget($target): void
    {
        $this->target = $target;
    }

    /**
     * Set an individual parameter to a value
     *
     * @param  string|int $name
     * @param  mixed      $value
     *
     * @return void
     */
    public function setParam($name, $value): void
    {
        if (is_array($this->params)) {
            // Arrays or objects implementing array access
            $this->params[$name] = $value;
        } else {
            // Objects
            $this->params->{$name} = $value;
        }
    }
}
