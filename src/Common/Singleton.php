<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

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
    public static function getInstance(): Singleton
    {
        return static::$instance ?? (static::$instance = static::initInstance());
    }

    /**
     * Initialization of class instance
     *
     * @return static
     */
    private static function initInstance()
    {
        return new static();
    }

    /**
     * Reset instance
     *
     * @return void
     */
    public static function resetInstance(): void
    {
        static::$instance = null;
    }

    /**
     * Disabled by access level
     */
    private function __construct()
    {
    }

    /**
     * Disabled by access level
     */
    private function __clone()
    {
    }

    /**
     * Disabled by access level
     */
    private function __wakeup()
    {
    }
}
