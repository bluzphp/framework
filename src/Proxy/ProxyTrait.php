<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Proxy;

use Bluz\Common\Singleton;

/**
 * ProxyTrait
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 */
trait ProxyTrait
{
    use Singleton;

    /**
     * Set or replace instance
     *
     * @param  mixed $instance
     *
     * @return void
     */
    public static function setInstance($instance)
    {
        static::$instance = $instance;
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string $method
     * @param  array  $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        if ($instance = static::getInstance()) {
            return $instance->$method(...$args);
        }
        return false;
    }
}
