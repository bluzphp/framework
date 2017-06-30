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
 * It's just null class
 *
 * @package  Bluz\Common
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Trait-Nil
 *
 * @method   null get($key)
 * @method   null set($key, $value)
 */
class Nil
{
    /**
     * Magic call
     *
     * @param  string $method
     * @param  array  $args
     *
     * @return null
     */
    public function __call($method, $args)
    {
        return null;
    }

    /**
     * Magic call for static
     *
     * @param  string $method
     * @param  array  $args
     *
     * @return null
     */
    public static function __callStatic($method, $args)
    {
        return null;
    }

    /**
     * Magic __get
     *
     * @param  string $key
     *
     * @return null
     */
    public function __get($key)
    {
        return null;
    }

    /**
     * Magic __set
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return null
     */
    public function __set($key, $value)
    {
        return null;
    }

    /**
     * Magic __isset
     *
     * @param  string $key
     *
     * @return false
     */
    public function __isset($key)
    {
        return false;
    }

    /**
     * Cast to empty string
     *
     * @return string
     */
    public function __toString()
    {
        return '';
    }
}
