<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
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
     * Handle dynamic, static calls to the object.
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        if ($instance = static::getInstance()) {
            return $instance->$method(...$args);
        } else {
            return false;
        }
    }
}
