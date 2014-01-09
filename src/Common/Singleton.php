<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Common;

/**
 * Singleton
 *
 * @category Bluz
 * @package  Common
 *
 * @author   Anton Shevchuk
 * @created  16.05.12 14:26
 */
trait Singleton
{
    /**
     * @var self
     */
    protected static $instance;

    /**
     * Disabled by access level
     * @return self
     */
    protected function __construct()
    {
        static::setInstance($this);
    }

    /**
     * setInstance
     *
     * @param self $instance
     * @throws Exception
     * @return self
     */
    final public static function setInstance($instance)
    {
        if ($instance instanceof static) {
            static::$instance = $instance;
        } else {
            throw new Exception(
                'First parameter for method `' . __METHOD__ . '`'.
                ' should be instance of `' . __CLASS__ . '`'
            );
        }
        return static::$instance;
    }

    /**
     * getInstance
     *
     * @throws Exception
     * @return static
     */
    final public static function getInstance()
    {
        return isset(static::$instance)
            ? static::$instance
            : static::$instance = new static;
    }

    /**
     * Disabled by access level
     */
    protected function __wakeup()
    {

    }

    /**
     * Disabled by access level
     */
    protected function __clone()
    {

    }
}
