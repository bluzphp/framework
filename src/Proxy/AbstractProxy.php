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

use Bluz\Application\Application;
use Bluz\Common\Exception\ComponentException;

/**
 * Abstract Proxy
 *
 * @package  Bluz\Proxy
 *
 * @author   Anton Shevchuk
 * @created  26.09.2014 10:51
 */
abstract class AbstractProxy
{
    /**
     * Init class instance
     *
     * @abstract
     * @throws ComponentException
     * @return mixed
     */
    protected static function initInstance()
    {
        throw new ComponentException(
            "Realization of method `initInstance()` is required for class `".get_called_class()."`"
        );
    }

    /**
     * Get class instance
     *
     * @throws ComponentException
     * @return mixed
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = static::initInstance();
            if (!$instance) {
                throw new ComponentException("Proxy class `".get_called_class()."` is not initialized");
            }
        }

        return $instance;
    }

    /**
     * Set or replace instance
     *
     * @param  mixed $replace
     * @return mixed
     */
    public static function setInstance($replace)
    {
        static $instance;
        $instance = $replace;
        return $instance;
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getInstance();

        // not need to check method exists, because we can use Nil class or magic methods
        return call_user_func_array(array($instance, $method), $args);
    }
}
