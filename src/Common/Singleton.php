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
     * Set or replace instance
     *
     * @param  mixed $instance
     * @return void
     */
    public static function setInstance($instance)
    {
        static::$instance = $instance;
    }

    /**
     * Get instance
     *
     * @return static
     */
    public static function getInstance()
    {
        return static::$instance ?? (static::$instance = static::initInstance());
    }

    /**
     * Initialization of class instance
     *
     * @return static
     */
    protected static function initInstance()
    {
        return new static;
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
