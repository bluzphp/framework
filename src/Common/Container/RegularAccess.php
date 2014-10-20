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
 * Implements regular access to container
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
trait RegularAccess
{
    /**
     * Set key/value pair
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->doSetContainer($key, $value);
    }

    /**
     * Get value by key
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->doGetContainer($key);
    }

    /**
     * Check contains key in container
     * @param string $key
     * @return bool
     */
    public function contains($key)
    {
        return $this->doContainsContainer($key);
    }

    /**
     * Delete value by key
     * @param string $key
     * @return void
     */
    public function delete($key)
    {
        $this->doDeleteContainer($key);
    }
}
