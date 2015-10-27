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
namespace Bluz\Proxy;

use Bluz\Common\Exception\ComponentException;

/**
 * Abstract Proxy
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Proxy
 */
abstract class AbstractProxy
{
    /**
     * @var array instances of classes
     */
    protected static $instances = array();

    /**
     * Init class instance
     *
     * @return mixed
     * @throws ComponentException
     */
    protected static function initInstance()
    {
        throw new ComponentException(
            "Realization of method `initInstance()` is required for class `". static::class ."`"
        );
    }

    /**
     * Get class instance
     *
     * @return mixed
     * @throws ComponentException
     */
    public static function getInstance()
    {
        $class = static::class;
        if (!isset(static::$instances[$class])) {
            static::$instances[$class] = static::initInstance();
            if (!static::$instances[$class]) {
                throw new ComponentException("Proxy class `$class` is not initialized");
            }
        }

        return static::$instances[$class];
    }

    /**
     * Set or replace instance
     *
     * @param  mixed $instance
     * @return void
     */
    public static function setInstance($instance)
    {
        static::$instances[static::class] = $instance;
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string $method
     * @param  array  $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getInstance();

        // not need to check method exists, because we can use Nil class or magic methods
        return $instance->$method(...$args);
    }
}
