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
 * @link     https://github.com/bluzphp/framework/wiki/Trait-Singleton
 *
 * @author   Anton Shevchuk
 * @created  16.05.12 14:26
 */
trait Singleton
{
    /**
     * @var static Singleton instance
     */
    protected static $instance;

    /**
     * Get instance
     * @return static::$instance
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
