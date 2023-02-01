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
    protected string $name;

    /**
     * @var mixed the event target
     */
    protected mixed $target = null;

    /**
     * @var array the event parameters
     */
    protected array $params = [];

    /**
     * Constructor
     *
     * Accept a target and its parameters.
     *
     * @param string $name Event name
     * @param mixed $target
     * @param array|null $params
     */
    public function __construct(string $name, mixed $target = null, array $params = null)
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
     * @return mixed
     */
    public function getTarget(): mixed
    {
        return $this->target;
    }

    /**
     * Overwrites parameters
     *
     * @param array $params
     *
     * @return void
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * Get all parameters
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Get an individual parameter
     *
     * If the parameter does not exist, the $default value will be returned.
     *
     * @param int|string $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getParam(int|string $name, mixed $default = null): mixed
    {
        return $this->params[$name] ?? $default;
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
     * @param mixed $target
     *
     * @return void
     */
    public function setTarget(mixed $target): void
    {
        $this->target = $target;
    }

    /**
     * Set an individual parameter to a value
     *
     * @param int|string $name
     * @param mixed $value
     *
     * @return void
     */
    public function setParam(int|string $name, mixed $value): void
    {
        $this->params[$name] = $value;
    }
}
