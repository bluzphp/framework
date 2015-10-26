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
namespace Bluz\Common;

/**
 * Singleton
 *
 * @package  Bluz\Common
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Trait-Singleton
 */
trait Singleton
{
    /**
     * @var static singleton instance
     */
    protected static $instance;

    /**
     * Get instance
     *
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
    protected function __construct()
    {
    }

    /**
     * Disabled by access level
     */
    protected function __clone()
    {
    }
}
