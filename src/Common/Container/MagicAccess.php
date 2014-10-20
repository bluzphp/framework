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
namespace Bluz\Common\Container;

/**
 * Implements magic access to container
 *
 * @package  Bluz\Common
 *
 * @method void doSetContainer($key, $value)
 * @method mixed doGetContainer($key)
 * @method bool doContainsContainer($key)
 * @method void doDeleteContainer($key)
 *
 * @author   Anton Shevchuk
 * @created  14.10.2014 10:11
 */
trait MagicAccess
{
    /**
     * Magic alias for set() regular method
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->doSetContainer($key, $value);
    }

    /**
     * Magic alias for get() regular method
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->doGetContainer($key);
    }

    /**
     * Magic alias for contains() regular method
     * @param  string $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->doContainsContainer($key);
    }

    /**
     * Magic alias for delete() regular method
     * @param  string $key
     * @return void
     */
    public function __unset($key)
    {
        $this->doDeleteContainer($key);
    }
}
