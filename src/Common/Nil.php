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
 * It's just null class
 *
 * @category Bluz
 * @package  Common
 *
 * @author   Anton Shevchuk
 * @created  15.01.13 09:50
 */
class Nil
{
    /**
     * Magic call
     *
     * @param $method
     * @param $args
     * @return null
     */
    public function __call($method, $args)
    {
        return null;
    }

    /**
     * Magic call for static
     *
     * @param $method
     * @param $args
     * @return null
     */
    public static function __callStatic($method, $args)
    {
        return null;
    }

    /**
     * __get
     *
     * @param $key
     * @return null
     */
    public function __get($key)
    {
        return null;
    }

    /**
     * __set
     *
     * @param $key
     * @param $value
     * @return null
     */
    public function __set($key, $value)
    {
        return null;
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return '';
    }
}
