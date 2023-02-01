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
 * Instance
 *
 * @package  Bluz\Common
 * @author   Anton Shevchuk
 */
trait Instance
{
    protected static array $instances = [];

    /**
     * Get instance
     * @return static
     */
    public static function getInstance(): static
    {
        if (!isset(static::$instances[static::class])) {
            static::$instances[static::class] = new static();
        }
        return static::$instances[static::class];
    }
}
