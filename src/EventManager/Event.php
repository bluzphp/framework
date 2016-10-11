<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
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
     * @param  string        $name Event name
     * @param  string|object $target
     * @param  array|object  $params
     */
    public function __construct($name, $target = null, $params = null)
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
    public function getName()
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
     * @return Event
     * @throws EventException
     */
    public function setParams($params)
    {
        if (!is_array($params) && !is_object($params)) {
            throw new EventException(
                sprintf(
                    'Event parameters must be an array or object; received "%s"',
                    gettype($params)
                )
            );
        }

        $this->params = $params;
        return $this;
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
     * @param  string $name
     * @return Event
     */
    public function setName($name)
    {
        $this->name = (string)$name;
        return $this;
    }

    /**
     * Set the event target/context
     *
     * @param  null|string|object $target
     * @return Event
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * Set an individual parameter to a value
     *
     * @param  string|int $name
     * @param  mixed      $value
     * @return Event
     */
    public function setParam($name, $value)
    {
        if (is_array($this->params)) {
            // Arrays or objects implementing array access
            $this->params[$name] = $value;
        } else {
            // Objects
            $this->params->{$name} = $value;
        }
        return $this;
    }
}
