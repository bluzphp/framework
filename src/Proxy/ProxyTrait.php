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
        return static::getInstance()->$method(...$args);
    }
}
